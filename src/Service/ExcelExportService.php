<?php
declare(strict_types=1);

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class ExcelExportService
{
    public function __construct(
        private TaskService $taskService,
    ) {
    }

    public function export(string $passphrase): StreamedResponse
    {
        $taskDTOs = $this->taskService->getAll($passphrase);
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'TITLE')
            ->setCellValue('C1', 'DESCRIPTION')
            ->setCellValue('D1', 'STATUS')
            ->setCellValue('E1', 'DUE DATE')
            ->setCellValue('F1', 'IS COMPLETE');

        $row = 2;
        foreach ($taskDTOs as $taskDTO) {
            $activeWorksheet
                ->setCellValue('A'.$row, $taskDTO->id)
                ->setCellValue('B'.$row, $taskDTO->title)
                ->setCellValue('C'.$row, $taskDTO->description)
                ->setCellValue('D'.$row, $taskDTO->taskStatus->value)
                ->setCellValue('E'.$row, $taskDTO->dueDate)
                ->setCellValue('F'.$row, $taskDTO->isComplete);
            $row++;
        }
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="todos.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}