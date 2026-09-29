<?php

namespace App\Repository;

use App\Entity\Incubation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IncubationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incubation::class);
    }

    public function findActiveForEntreprise(int $userId): ?Incubation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.entreprise = :uid')
            ->andWhere('i.statutGlobal = :statut')
            ->setParameter('uid', $userId)
            ->setParameter('statut', Incubation::STATUT_EN_COURS)
            ->getQuery()->getOneOrNullResult();
    }
}
