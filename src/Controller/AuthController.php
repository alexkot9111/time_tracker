<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\AuthService;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthController extends Controller
{
    private JWTTokenManagerInterface $JWTManager;
    private EntityManagerInterface $entityManager;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private AuthService $authService;

    public function __construct(
        JWTTokenManagerInterface $JWTManager,
        EntityManagerInterface $em,
        RefreshTokenManagerInterface $refreshTokenManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        AuthService $authService
    )
    {
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

        try {
            // Call AuthService to perform the login
            $tokens = $this->authService->login($data);

            // Set Response with access_token
            $response = new JsonResponse(['access_token' => $tokens['access_token']], Response::HTTP_CREATED);

            // Set HttpOnly cookie for refresh token
            return $this->authService->setRefreshTokenCookie($response, $tokens['refresh_token']);
        } catch (AuthenticationException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/logout', name: 'api_logout', methods: ['GET'])]
    public function logout(Request $request): JsonResponse
    {
        $response = new JsonResponse(['message' => 'Logged out successfully.'], Response::HTTP_OK);
        return $this->authService->clearRefreshTokenCookie($response);
    }

    #[Route('/token/refresh', name: 'api_refresh_token', methods: ['GET'])]
    public function refresh(Request $request): JsonResponse
    {
        $refreshTokenString = $request->cookies->get('refresh_token');

        try {
            // Call AuthService to perform the refresh
            $tokens = $this->authService->refresh($refreshTokenString);

            // Set Response with access_token
            $response = new JsonResponse(['access_token' => $tokens['access_token']], Response::HTTP_OK);

            // Set HttpOnly cookie for refresh token
            return $this->authService->setRefreshTokenCookie($response, $tokens['refresh_token']);
        } catch (AuthenticationException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}