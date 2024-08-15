<?php

declare(strict_types=1);

namespace App\DTO;

use App\Serializer\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;

use Symfony\Component\Validator\Constraints as Assert;

final class Passphrase
{
    #[Groups([AccessGroup::PASSPHRASE_CREATE_RESPONSE, AccessGroup::TASK_READ])]
    public string $passphrase;

    #[Assert\Type('int')]
    #[Groups([AccessGroup::PASSPHRASE_CREATE, AccessGroup::TASK_READ])]
    public int $id;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setPassphrase(string $passphrase): self
    {
        $this->passphrase = $passphrase;

        return $this;
    }


}
