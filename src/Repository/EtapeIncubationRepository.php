<?php

namespace App\Repository;

use App\Entity\EtapeIncubation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EtapeIncubationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtapeIncubation::class);
    }

    public function findByReferent(int $userId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.referent = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('e.dateMiseAJour', 'DESC')
            ->getQuery()->getResult();
    }
}
