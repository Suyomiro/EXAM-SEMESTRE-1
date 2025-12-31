<?php

namespace App\Repository;

use App\Entity\Gestionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gestionnaire::class);
    }

    public function findOneByEmail(string $email): ?Gestionnaire
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
