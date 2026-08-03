<?php

namespace App\Repository;

use App\Entity\DemandeReduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DemandeReductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeReduction::class);
    }

    public function findByEtudiant(int $userId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.etudiant = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
