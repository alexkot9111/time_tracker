<?php

namespace App\Controller;

use App\Config\UserRole;
use App\Entity\User;
use App\Service\UserService;
use App\Repository\UserRepository;
use App\Service\CurrentCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
#[Route('/user')]
class UserController extends Controller
{
    private $currentCompanyService;
    private $userRepository;
    private $userService;
    private $serializer;

    public function __construct(
        CurrentCompanyService $currentCompanyService,
        UserRepository $userRepository,
        UserService $userService,
        SerializerInterface $serializer
    ) {
        $this->currentCompanyService = $currentCompanyService;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $currentCompany = $this->currentCompanyService->getCurrentCompany();
        $users = $this->userRepository->findByCompanyId($currentCompany->getId());
        $jsonUsers = $this->serializer->serialize($users, 'json', ['groups' => ['user']]);
        return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'app_user_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $createUser = $this->userService->createUser($data, UserRole::ROLE_USER);
        return new JsonResponse($createUser['data'], $createUser['code']);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return new JsonResponse([
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName()
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(Request $request, User $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $editUser = $this->userService->editUser($data, $user);
        return new JsonResponse($editUser['data'], $editUser['code']);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // toDo Authorization and token check
        //if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
