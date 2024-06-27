<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

abstract class Controller extends AbstractController
{
    public function testDbConnection(EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();

        try {
            $connection->beginTransaction();
            if ($connection->isConnected()) {
                return new Response('Database connection is active.');
            }
        } catch (\Exception $e) {
            return new Response('Database connection error: ' . $e->getMessage());
        }

        return new Response('Could not connect to the database.');
    }
}