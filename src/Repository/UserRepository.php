<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByCompanyId(?int $companyId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.company = :companyId')
            ->setParameter('companyId', $companyId)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }
}
