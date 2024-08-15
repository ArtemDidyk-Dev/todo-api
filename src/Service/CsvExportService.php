<?php

namespace App\Service;

use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class CsvExportService implements ExportInterface
{
    public function __construct(
        private TaskServiceInterface $taskService
    ) {
    }

    public function export(string $passphrase): StreamedResponse
    {
        $taskDTOs = $this->taskService->getAll($passphrase);
        $response = new StreamedResponse(function () use ($taskDTOs) {
            $stream = fopen('php://output', 'w');
            if ($stream === false) {
                throw new \RuntimeException('Could not open stream for writing');
            }
            $csv = Writer::createFromStream($stream);
            $csv->insertOne(['ID', 'Title', 'Description', 'Status', 'Due Date', 'Is Complete']);
            foreach ($taskDTOs as $taskDTO) {
                $dueDate = $taskDTO->dueDate instanceof \DateTimeImmutable ? $taskDTO->dueDate->format(
                    'Y-m-d H:i:s'
                ) : '';
                $csv->insertOne([
                    $taskDTO->id,
                    $taskDTO->title,
                    $taskDTO->description,
                    $taskDTO->status->value,
                    $dueDate,
                    $taskDTO->isComplete,
                ]);
            }
        });

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        $response->headers->set('Expires', '0');
        $response->headers->set('Pragma', 'public');

        return $response;
    }
}