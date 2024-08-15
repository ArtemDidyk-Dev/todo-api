<?php

declare(strict_types=1);

namespace App\Enum;

enum ExportEnum: string
{
    case EXCEL = 'exel';
    case CSV = 'csv';
}
