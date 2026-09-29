<?php

namespace App\Repository;

use App\Entity\Matiere;
use App\Pagination\PaginatedResult;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matiere::class);
    }

    public function findActivesPaginated(int $page, ?string $filiere = null): PaginatedResult
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.active = true')
            ->orderBy('m.filiere', 'ASC')
            ->addOrderBy('m.nom', 'ASC');

        if ($filiere) {
            $qb->andWhere('m.filiere = :filiere')->setParameter('filiere', $filiere);
        }

        return Paginator::paginate($qb, $page);
    }

    /** @return string[] */
    public function findDistinctFilieres(): array
    {
        return array_column(
            $this->createQueryBuilder('m')
                ->select('DISTINCT m.filiere')
                ->andWhere('m.active = true')
                ->andWhere('m.filiere IS NOT NULL')
                ->orderBy('m.filiere', 'ASC')
                ->getQuery()->getScalarResult(),
            'filiere',
        );
    }
}
