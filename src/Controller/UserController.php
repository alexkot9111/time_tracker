<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CurrentCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user')]
class UserController extends Controller
{
    private $currentCompanyService;
    private $userRepository;

    public function __construct(CurrentCompanyService $currentCompanyService, UserRepository $userRepository)
    {
        $this->currentCompanyService = $currentCompanyService;
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $currentCompany = $this->currentCompanyService->getCurrentCompany();
        $users = $userRepository->findByCompanyId($currentCompany->getId());
        $jsonUsers = $serializer->serialize($users, 'json', ['groups' => ['user']]);
        return new JsonResponse($jsonUsers, 201, [], true);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer): JsonResponse
    {
        $currentCompany = $this->currentCompanyService->getCurrentCompany();
        $data = json_decode($request->getContent(), true);

        if(empty($data['email'])) {
            return new JsonResponse(['errors' => ['email' => 'Email can not be empty']], 400);
        }

        // Check if email already exist
        $existingUser = $this->userRepository->findOneByEmail($data['email']);
        if ($existingUser) {
            return new JsonResponse(['errors' => ['email' => 'Email already exists']], 400);
        }

        // Set properties of the User entity
        $user = new User();
        $user->setCompanyId($currentCompany);
        $user->setEmail($data['email']);
        $user->setFirstName($data['first_name'] ?? '');
        $user->setLastName($data['last_name'] ?? '');
        $user->setCreated(new \DateTime());

        // Validate the User entity
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            // Transform Symfony's ConstraintViolationListInterface into an array of error messages
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            // Return JSON response with error messages and status code 400 (Bad Request)
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $jsonPost = $serializer->serialize($user, 'json', ['groups' => ['user']]);

        return new JsonResponse($jsonPost, 201, [], true);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
