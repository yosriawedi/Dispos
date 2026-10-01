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

    /** Candidatures reçues sur toutes les offres publiées par une entreprise. */
    public function countByOffreEntreprise(int $entrepriseUserId): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->join('c.offre', 'o')
            ->andWhere('o.entreprise = :uid')
            ->setParameter('uid', $entrepriseUserId)
            ->getQuery()->getSingleScalarResult();
    }
}
