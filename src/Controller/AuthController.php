<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth')]
class AuthController extends Controller
{
    #[Route('/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(): JsonResponse
    {

    }

    #[Route('/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(): JsonResponse
    {

    }
}