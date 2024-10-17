<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthService
{
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
}