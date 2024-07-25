<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskStatusEnum: string
{
    case CREATED = 'created';
    case DONE = 'done';
    case FAILED = 'failed';
    case IN_PROGRESS = 'in_progress';
}
