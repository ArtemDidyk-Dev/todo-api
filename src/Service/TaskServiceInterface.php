<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Meta;
use App\DTO\Passphrase;
use App\DTO\Task;
use Symfony\Component\HttpFoundation\Request;

interface TaskServiceInterface
{
    public function createTask(Passphrase $passphraseDTO, Task $taskDTO): Task;

    public function getTasks(Request $request, string $passphrase, Meta $meta): TaskList;

    public function getTask(Passphrase $passphraseDTO, int $id): Task;

    public function destroyTask(Passphrase $passphraseDTO, int $id): void;

    public function updateTask(Passphrase $passphraseDTO, int $id, Task $taskDTO): Task;
}
