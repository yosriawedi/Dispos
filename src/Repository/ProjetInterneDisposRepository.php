<?php

namespace App\Repository;

use App\Entity\ProjetInterneDispos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjetInterneDisposRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjetInterneDispos::class);
    }

    public function findOuverts(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.statut = :statut')
            ->setParameter('statut', ProjetInterneDispos::STATUT_OUVERT)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
