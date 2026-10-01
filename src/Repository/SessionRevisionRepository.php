<?php

namespace App\Repository;

use App\Entity\SessionRevision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SessionRevisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionRevision::class);
    }

    public function findAVenir(int $limit = 10): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebut > :now')
            ->andWhere('s.statut = :statut')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('statut', SessionRevision::STATUT_PLANIFIEE)
            ->orderBy('s.dateDebut', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }

    public function findByFormateur(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.formateur = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('s.dateDebut', 'DESC')
            ->getQuery()->getResult();
    }

    public function findByMatiere(int $matiereId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.matiere = :mid')
            ->andWhere('s.statut IN (:statuts)')
            ->setParameter('mid', $matiereId)
            ->setParameter('statuts', [SessionRevision::STATUT_PLANIFIEE, SessionRevision::STATUT_EN_COURS])
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()->getResult();
    }
}
