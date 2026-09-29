<?php

namespace App\Repository;

use App\Entity\DemandeConsultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DemandeConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeConsultation::class);
    }

    public function findByIncubation(int $incubationId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.incubation = :iid')
            ->setParameter('iid', $incubationId)
            ->orderBy('d.dateDemande', 'DESC')
            ->getQuery()->getResult();
    }
}
