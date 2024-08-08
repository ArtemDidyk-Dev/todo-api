<?php

declare(strict_types=1);

namespace App\DTO;

final class Meta
{
    public function __construct(
        public int $currentPage = 1,
        public int $itemsPerPage = 10,
        public int $totalCount = 0,
        public int $totalPages = 0,
    ) {
    }
}
