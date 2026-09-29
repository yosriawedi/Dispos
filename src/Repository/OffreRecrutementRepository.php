<?php

namespace App\Repository;

use App\Entity\OffreRecrutement;
use App\Pagination\PaginatedResult;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OffreRecrutementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreRecrutement::class);
    }

    public function findPublieesPaginated(int $page, ?string $typeContrat = null): PaginatedResult
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.statut = :statut')
            ->setParameter('statut', OffreRecrutement::STATUT_PUBLIEE)
            ->orderBy('o.createdAt', 'DESC');

        if ($typeContrat) {
            $qb->andWhere('o.typeContrat = :typeContrat')->setParameter('typeContrat', $typeContrat);
        }

        return Paginator::paginate($qb, $page);
    }

    public function findByEntreprise(int $userId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.entreprise = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
