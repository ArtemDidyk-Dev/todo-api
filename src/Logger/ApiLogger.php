<?php

declare(strict_types=1);

namespace App\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ApiLogger
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function log(Request $request, Response $response): void
    {
        $this->logger->info($response);
        $this->logger->info($request);
    }
}
