<?php

declare(strict_types=1);

namespace App\DTO;

use App\Serializer\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;

final class Meta
{
    public function __construct(
        #[Groups([AccessGroup::TASK_READ])]
        public int $currentPage = 1,
        #[Groups([AccessGroup::TASK_READ])]
        public int $itemsPerPage = 10,
        #[Groups([AccessGroup::TASK_READ])]
        public int $totalCount = 0,
        #[Groups([AccessGroup::TASK_READ])]
        public int $totalPages = 0,
    ) {
    }
}
