<?php

namespace App\Repository;

use App\Entity\CandidatureRecrutement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CandidatureRecrutementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidatureRecrutement::class);
    }

    public function findByCandidat(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.candidat = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
