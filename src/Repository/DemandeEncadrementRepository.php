<?php

namespace App\Repository;

use App\Entity\DemandeEncadrement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DemandeEncadrementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeEncadrement::class);
    }

    public function findByEtudiant(int $userId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.etudiant = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()->getResult();
    }

    public function findEnAttente(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.statut = :statut')
            ->setParameter('statut', DemandeEncadrement::STATUT_EN_ATTENTE)
            ->orderBy('d.createdAt', 'ASC')
            ->getQuery()->getResult();
    }
}
