<?php

namespace App\Repository;

use App\Entity\Matiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matiere::class);
    }

    public function findActives(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.active = true')
            ->orderBy('m.filiere', 'ASC')
            ->addOrderBy('m.nom', 'ASC')
            ->getQuery()->getResult();
    }
}
