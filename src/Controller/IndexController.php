<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends Controller
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'test' => 'ok',
        ]);
    }

}