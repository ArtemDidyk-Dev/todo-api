<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Meta;
use App\DTO\Passphrase;
use App\DTO\Task as TaskDTO;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task as TaskEntity;

interface TaskServiceInterface
{
    public function createTask(Passphrase $passphraseDTO, TaskDTO $taskDTO): TaskDTO;

    public function getTasks(Request $request, string $passphrase, Meta $meta): TaskList;

    public function getTask(Passphrase $passphraseDTO, int $id): TaskDTO;

    public function destroyTask(Passphrase $passphraseDTO, int $id): void;

    public function updateTask(Passphrase $passphraseDTO, TaskEntity $task, TaskDTO $taskDTO): TaskDTO;

    public function getAll(string $passphrase): array;
}
