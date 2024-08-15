<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface ExportInterface
{
    public function export(string $passphrase): StreamedResponse;
}