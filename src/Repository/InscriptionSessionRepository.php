<?php

namespace App\Repository;

use App\Entity\InscriptionSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InscriptionSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InscriptionSession::class);
    }

    public function findByEtudiant(int $userId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.etudiant = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
