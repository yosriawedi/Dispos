<?php

namespace App\Repository;

use App\Entity\ProjetInterneDispos;
use App\Pagination\PaginatedResult;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjetInterneDisposRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjetInterneDispos::class);
    }

    public function findOuvertsPaginated(int $page, ?string $domaine = null): PaginatedResult
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.statut = :statut')
            ->setParameter('statut', ProjetInterneDispos::STATUT_OUVERT)
            ->orderBy('p.createdAt', 'DESC');

        if ($domaine) {
            $qb->andWhere('p.domaine = :domaine')->setParameter('domaine', $domaine);
        }

        return Paginator::paginate($qb, $page);
    }

    /** @return string[] */
    public function findDistinctDomaines(): array
    {
        return array_column(
            $this->createQueryBuilder('p')
                ->select('DISTINCT p.domaine')
                ->andWhere('p.statut = :statut')
                ->andWhere('p.domaine IS NOT NULL')
                ->setParameter('statut', ProjetInterneDispos::STATUT_OUVERT)
                ->orderBy('p.domaine', 'ASC')
                ->getQuery()->getScalarResult(),
            'domaine',
        );
    }
}
