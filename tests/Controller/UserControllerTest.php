<?php

namespace App\Test\Controller;

use App\Config\UserRole;
use App\Entity\User;
use App\Service\UserService;
use App\Controller\UserController;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(UserController::class)]
class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private $userService;
    private string $path = '/api/user/';

    private static $dummData = [
        'email' => 'alex@alex.com',
        'first_name' => 'Alex',
        'last_name' => 'Test',
    ];

    private static $dummDataEdit = [
        'first_name' => 'Alex2',
        'last_name' => 'Test2',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Create the client for making requests
        $this->client = static::createClient();

        // Get the entity manager
        $this->manager = static::getContainer()->get('doctrine')->getManager();

        // Get the UserService from the container
        $this->userService = static::getContainer()->get(UserService::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testIndex(): void
    {
        // Send GET request to the index route
        $this->client->request('GET', $this->path);

        // Output the response content for debugging
        $responseContent = $this->client->getResponse()->getContent();
        $responseStatus = $this->client->getResponse()->getStatusCode();

        // Assert that the response was successful
        $this->assertEquals(Response::HTTP_OK, $responseStatus, 'Expected status code 200 for users list');

        // Assert that the response is valid JSON
        $this->assertJson($responseContent);

        // Additionally, check if the returned JSON contains the expected data
        $jsonData = json_decode($responseContent, true);

        // Assert that the response data is an array (depending on the logic of your controller)
        $this->assertIsArray($jsonData, 'Response data should be an array');
        $this->assertNotEmpty($jsonData, 'Response data should not be empty');
    }

    public function testNew(): void
    {
        // Send POST request to create a new user
        $this->client->request('POST', sprintf('%snew', $this->path), [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(self::$dummData));

        // Output the response content for debugging
        $responseContent = $this->client->getResponse()->getContent();
        $responseStatus = $this->client->getResponse()->getStatusCode();

        // Add assertions to verify the response
        $this->assertEquals(Response::HTTP_CREATED, $responseStatus, 'Expected status code 201 for user creation');
        $this->assertJson($responseContent, 'Response should be in JSON format');
    }

    public function testShow(): void
    {
        // Get User Fixture
        $fixture = $this->userService->getUserModel(self::$dummData, UserRole::ROLE_USER);

        // Save User
        $this->manager->persist($fixture);
        $this->manager->flush();

        // Send GET request
        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        // Output the response content for debugging
        $responseContent = $this->client->getResponse()->getContent();
        $responseStatus = $this->client->getResponse()->getStatusCode();

        // Add assertions to verify the response
        $this->assertEquals(Response::HTTP_OK, $responseStatus, 'Expected status code 200 for user show');
        $this->assertJson($responseContent, 'Response should be in JSON format');
    }

    public function testEdit(): void
    {
        // Get User Fixture
        $fixture = $this->userService->getUserModel(self::$dummData, UserRole::ROLE_USER);

        // Save User
        $this->manager->persist($fixture);
        $this->manager->flush();

        // Send PUT request
        $this->client->request('PUT', sprintf('%s%s', $this->path, $fixture->getId()), [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(self::$dummDataEdit));

        // Output the response content for debugging
        $responseContent = $this->client->getResponse()->getContent();
        $responseStatus = $this->client->getResponse()->getStatusCode();

        // Add assertions to verify the response
        $this->assertEquals(Response::HTTP_OK, $responseStatus, 'Expected status code 200 for user show');
        $this->assertJson($responseContent, 'Response should be in JSON format');

        // Add assertions to verify the data
        $jsonData = json_decode($responseContent, true);
        $this->assertEquals(self::$dummDataEdit['first_name'], $jsonData['first_name'], 'First names should be equal');
        $this->assertEquals(self::$dummDataEdit['last_name'], $jsonData['last_name'], 'Last names should be equal');
    }

    public function testRemove(): void
    {
        // Get User Fixture
        $fixture = $this->userService->getUserModel(self::$dummData, UserRole::ROLE_USER);

        // Save User
        $this->manager->persist($fixture);
        $this->manager->flush();
        $userId = $fixture->getId();

        // Assert the user exists in the database
        $this->assertNotNull($this->manager->getRepository(User::class)->find($userId));

        // Send DELETE request to the delete route
        $this->client->request('DELETE', sprintf('%s%s', $this->path, $userId));

        // Check that the response is a redirection (to 'app_user_index')
        $this->assertResponseRedirects($this->path, Response::HTTP_SEE_OTHER);

        // Clear entity manager to ensure fresh data is fetched
        $this->manager->clear();

        // Assert that the user no longer exists in the database
        $this->assertNull($this->manager->getRepository(User::class)->find($userId), 'User should be deleted');
    }
}
