<?php

declare(strict_types=1);

namespace App\Enum;

enum PriorityEnum: int
{
    case Highest = 1;
    case High = 2;
    case Medium = 3;
    case Low = 4;
    case Lowest = 5;
}
