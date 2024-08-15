<?php

namespace App\Service;

use App\Enum\ExportEnum;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class ExportFactory
{
    public function __construct(
        private ExcelExportService $excelExportService,
        private CsvExportService $csvExportService,
    ) {
    }

    public function createExport(string $type, string $passphrase): StreamedResponse
    {
        $exportType = ExportEnum::tryFrom($type);
        if ($exportType === null) {
            throw new \InvalidArgumentException('type not found');
        }
        return match ($exportType) {
            ExportEnum::EXCEL => $this->excelExportService->export($passphrase),
            ExportEnum::CSV => $this->csvExportService->export($passphrase),
        };
    }
}