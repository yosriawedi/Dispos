<?php

namespace App\Repository;

use App\Entity\OffreCompetence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OffreCompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreCompetence::class);
    }

    public function findByEtudiant(int $userId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.etudiant = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
