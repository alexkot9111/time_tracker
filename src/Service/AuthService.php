<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

class AuthService
{
    private $userRepository;
    private $passwordHasher;
    private $JWTManager;
    private $entityManager;
    private $refreshTokenManager;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $JWTManager,
        EntityManagerInterface $entityManager,
        RefreshTokenManagerInterface $refreshTokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->JWTManager = $JWTManager;
        $this->entityManager = $entityManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function login(array $data): array
    {
        if (!isset($data['email']) || !isset($data['password'])) {
            throw new AuthenticationException('Email and password are required');
        }

        $email = $data['email'];
        $password = $data['password'];

        // Find user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        // Generate access token
        $accessToken = $this->JWTManager->create($user);

        // Generate refresh token
        $refreshToken = $this->setRefreshToken($user);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];
    }

    public function refresh(string $refreshTokenString): array
    {
        if (!$refreshTokenString) {
            throw new AuthenticationException('Refresh token is required');
        }

        // Find the refresh token entity
        $refreshToken = $this->refreshTokenManager->get($refreshTokenString);

        if (!$refreshToken || !$refreshToken->isValid()) {
            throw new AuthenticationException('Invalid or expired refresh token');
        }

        // Get the user associated with the refresh token using email
        $user = $this->userRepository->findOneBy(['email' => $refreshToken->getUsername()]);

        if (!$user) {
            throw new AuthenticationException('User not found');
        }

        // Generate a new access token
        $newAccessToken = $this->JWTManager->create($user);

        // Generate a new refresh token
        $newRefreshToken = $this->setRefreshToken($user, $refreshToken);

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken->getRefreshToken(),
        ];
    }

    function setRefreshToken(User $user, RefreshTokenInterface $oldRefreshToken = null): RefreshTokenInterface
    {
        // Create/Update refresh token
        $refreshToken = $oldRefreshToken ?? $this->refreshTokenManager->create();

        $refreshToken->setRefreshToken(bin2hex(random_bytes(32)));
        $refreshToken->setUsername($user->getEmail());
        $refreshToken->setValid((new \DateTime())->modify('+30 days'));

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }

    function setRefreshTokenCookie($response, $refreshToken): JsonResponse
    {
        $response->headers->setCookie(
            new Cookie(
                'refresh_token',
                $refreshToken,
                strtotime('+30 days'),  // Expires in 30 days
                '/',
                null,
                false,  // Secure (only send over HTTPS) false for local
                true,  // HttpOnly (not accessible via JavaScript)
                false,
                'Strict'
            )
        );

        return $response;
    }

    public function clearRefreshTokenCookie(JsonResponse $response): JsonResponse
    {
        $response->headers->clearCookie(
            'refresh_token',
            '/',
            null,
            false, // Secure (set to true if your site is HTTPS)
            true,  // HttpOnly
            'Strict'
        );

        return $response;
    }

}