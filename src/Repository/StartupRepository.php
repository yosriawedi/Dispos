<?php

namespace App\Repository;

use App\Entity\Startup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StartupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Startup::class);
    }

    public function findLatest(int $limit = 6): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findBySector(string $sector): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.sector = :sector')
            ->setParameter('sector', $sector)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
