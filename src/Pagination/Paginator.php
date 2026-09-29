<?php

namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Thin wrapper around Doctrine's own Paginator (no extra bundle needed)
 * so repository methods can paginate a QueryBuilder with one call.
 */
class Paginator
{
    public const DEFAULT_PER_PAGE = 9;

    public static function paginate(QueryBuilder $qb, int $page, int $perPage = self::DEFAULT_PER_PAGE): PaginatedResult
    {
        $page = max(1, $page);

        $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

        $doctrinePaginator = new DoctrinePaginator($qb);

        return new PaginatedResult(
            iterator_to_array($doctrinePaginator->getIterator()),
            $page,
            \count($doctrinePaginator),
            $perPage,
        );
    }
}
