<?php

declare(strict_types=1);

namespace App\Service;
use App\DTO\Passphrase as PassphraseDTO;

interface PassphraseInterface
{
    public function createPassphrase(): PassphraseDTO;
}
