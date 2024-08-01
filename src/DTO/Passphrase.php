<?php

declare(strict_types=1);

namespace App\DTO;

use App\Serializer\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Passphrase
{
    public function __construct(
        #[Assert\NotBlank]
        #[Groups([AccessGroup::PASSPHRASE_CREATE_RESPONSE, AccessGroup::TASKS_READ])]
        public string $passphrase,
    ) {
    }
}
