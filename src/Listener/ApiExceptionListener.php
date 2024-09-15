<?php

namespace App\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ApiExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
    )
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = [
            'status' => 'error',
            'message' => 'An error occurred'
        ];
    }
}
