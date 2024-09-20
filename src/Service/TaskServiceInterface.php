<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Meta;
use App\DTO\Passphrase;
use App\DTO\Task as TaskDTO;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task as TaskEntity;
use App\Entity\Passphrase as PassphraseEntity;
interface TaskServiceInterface
{
    public function createTask(PassphraseEntity $passphrase, TaskDTO $taskDTO): TaskDTO;

    public function getTasks(Request $request, PassphraseEntity $passphrase, Meta $meta): TaskList;

    public function getTask(PassphraseEntity $passphrase, TaskEntity $task): TaskDTO;

    public function destroyTask(PassphraseEntity $passphrase, TaskEntity $task): void;

    public function updateTask(Passphrase $passphraseDTO, TaskEntity $task, TaskDTO $taskDTO): TaskDTO;

    public function getAll(PassphraseEntity $passphrase): array;
}
