<?php

namespace App\Service;

use App\Entity\Passphrase;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ExportInterface
{
    public function export(Passphrase $passphrase): StreamedResponse;
}
