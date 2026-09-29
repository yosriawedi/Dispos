<?php

namespace App\Pagination;

class PaginatedResult
{
    public function __construct(
        public readonly array $items,
        public readonly int $currentPage,
        public readonly int $totalItems,
        public readonly int $perPage,
    ) {
    }

    public function getTotalPages(): int
    {
        return (int) max(1, ceil($this->totalItems / $this->perPage));
    }

    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->getTotalPages();
    }
}
