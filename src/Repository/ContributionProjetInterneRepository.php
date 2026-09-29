<?php

namespace App\Repository;

use App\Entity\ContributionProjetInterne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContributionProjetInterneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContributionProjetInterne::class);
    }

    public function findByEtudiant(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.etudiant = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
