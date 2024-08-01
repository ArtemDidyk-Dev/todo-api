<?php

declare(strict_types=1);

namespace App\Service;
use App\DTO\PassphraseResponse as PassphraseResponseDTO;
interface PassphraseInterface
{
    public function createPassphrase(): PassphraseResponseDTO;
}
