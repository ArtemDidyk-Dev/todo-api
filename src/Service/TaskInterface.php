<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Meta;
use App\DTO\Passphrase;
use App\DTO\Task;

interface TaskInterface
{
    public function createTask(Passphrase $passphraseDTO, Task $taskDTO): Task;

    public function getTasks(string $passphrase, Meta $meta): TaskList;

    public function getTask(Passphrase $passphraseDTO, int $id): Task;

    public function destroyTask(Passphrase $passphraseDTO, int $id): void;

    public function updateTask(Passphrase $passphraseDTO, int $id, Task $taskDTO): Task;
}
