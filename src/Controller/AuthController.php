<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use App\Service\AuthService;

class AuthController extends Controller
{
    private RefreshTokenRepository $tokenRepository;
    private JWTTokenManagerInterface $JWTManager;
    private EntityManagerInterface $entityManager;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private AuthService $authService;

    public function __construct(
        RefreshTokenRepository $tokenRepository,
        JWTTokenManagerInterface $JWTManager,
        EntityManagerInterface $em,
        RefreshTokenManagerInterface $refreshTokenManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        AuthService $authService
    )
    {
        $this->tokenRepository = $tokenRepository;
        $this->JWTManager = $JWTManager;
        $this->entityManager = $em;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->authService = $authService;
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['email'];
        $password = $data['password'];

        // Find user by email instead of username
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        // Generate access token
        $accessToken = $this->JWTManager->create($user);

        // Create refresh token
        $refreshToken = $this->refreshTokenManager->create();
        $refreshToken->setRefreshToken(uniqid());
        $refreshToken->setUsername($user->getEmail());  // Store the email
        $refreshToken->setValid((new \DateTime())->modify('+30 days'));
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        // Create response
        $response = new JsonResponse([
            'access_token' => $accessToken
        ]);

        // Set HttpOnly cookie for refresh token
        return $this->authService->setRefreshTokenCookie($response, $refreshToken->getRefreshToken());

    }

    #[Route('/token/refresh', name: 'api_refresh_token', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        // Get the refresh token from the HttpOnly cookie
        $refreshTokenString = $request->cookies->get('refresh_token');

        if (!$refreshTokenString) {
            return new JsonResponse(['message' => 'Refresh token is required'], Response::HTTP_BAD_REQUEST);
        }

        // Find the refresh token entity
        $refreshToken = $this->refreshTokenManager->get($refreshTokenString);

        if (!$refreshToken || !$refreshToken->isValid()) {
            return new JsonResponse(['message' => 'Invalid or expired refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        // Get the user associated with the refresh token using email
        $user = $this->userRepository->findOneBy(['email' => $refreshToken->getUsername()]);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Generate a new access token
        $newAccessToken = $this->JWTManager->create($user);

        // Refresh token and replace the old one
        $newRefreshToken = uniqid();
        $refreshToken->setRefreshToken($newRefreshToken);
        $refreshToken->setValid((new \DateTime())->modify('+30 days'));
        $this->entityManager->flush();

        // Create response
        $response = new JsonResponse([
            'access_token' => $newAccessToken
        ]);

        // Set HttpOnly cookie for refresh token
        return $this->authService->setRefreshTokenCookie($response, $refreshToken->getRefreshToken());
    }
}