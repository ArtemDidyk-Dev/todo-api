<?php

namespace App\Service;

use App\Enum\ExportEnum;

final readonly class ExportFactory
{

    public  function createExport(string $type, TaskServiceInterface $taskService): ExportInterface
    {
       $exportType = ExportEnum::tryFrom($type);
       if ($exportType === null) {
           throw new \InvalidArgumentException('type not found');
       }
        return match ($exportType) {
            ExportEnum::EXCEL => new ExcelExportService($taskService),
            ExportEnum::CSV => new CsvExportService($taskService),
        };
    }
}