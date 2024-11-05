<?php

namespace App\Service;

use App\Config\UserRole;
use App\Config\UserStatus;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService
{
    private $entityManager;
    private $userRepository;
    private $validator;
    private $serializer;
    private $currentCompanyService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        CurrentCompanyService $currentCompanyService
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->currentCompanyService = $currentCompanyService;
    }

    public function getUserModel(array $data, UserRole $userRole): User
    {
        // Get current company
        $currentCompany = $this->currentCompanyService->getCurrentCompany();

        // Set values
        $user = new User();
        $user->setCompanyId($currentCompany);
        $user->setEmail($data['email']);
        $user->setFirstName($data['first_name'] ?? '');
        $user->setLastName($data['last_name'] ?? '');
        $user->setCreated(new \DateTime());
        $user->setRole($userRole);
        $user->setStatus(UserStatus::STATUS_NOT_ACTIVE);

        // Return User Model
        return $user;
    }

    public function createUser(array $data, UserRole $userRole): array
    {
        if (empty($data['email'])) {
            return [
                'data' => ['errors' => ['email' => 'Email cannot be empty']],
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }

        // Check if email already exists
        $existingUser = $this->userRepository->findOneByEmail($data['email']);
        if ($existingUser) {
            return [
                'data' => ['errors' => ['email' => 'Email already exists']],
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }

        // Set properties of the User entity
        $user = $this->getUserModel($data, $userRole);

        // Validate the User entity
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            // Transform Symfony's ConstraintViolationListInterface into an array of error messages
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            // Return array with error messages
            return [
                'data' => ['errors' => $errorMessages],
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $normalizedUser = $this->serializer->normalize($user, null, ['groups' => ['user']]);

        return [
            'data' => ['user' => $normalizedUser],
            'code' => Response::HTTP_CREATED
        ];
    }

    public function editUser(array $data, User $user): array
    {
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);

        // Validate the User entity
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            // Transform Symfony's ConstraintViolationListInterface into an array of error messages
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            // Return array with error messages
            return [
                'data' => ['errors' => $errorMessages],
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $normalizedUser = $this->serializer->normalize($user, null, ['groups' => ['user']]);

        return [
            'data' => ['user' => $normalizedUser],
            'code' => Response::HTTP_CREATED
        ];
    }
}
