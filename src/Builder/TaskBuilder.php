<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task as TaskDto;
use App\Entity\Passphrase as PassphraseEntity;
use App\Entity\Task as TaskEntity;

class TaskBuilder
{
    public function mapToDto(TaskEntity $task): TaskDto
    {
        return new TaskDto(
            title: $task->getTitle(),
            description: $task->getDescription(),
            dueDate: $task->getDueDate()?->format('Y-m-d H:i:s'),
            taskStatus: $task->getStatus(),
            priority: $task->getPriority(),
            id: $task->getId(),
            isComplete: $task->isComplete(),
            passphrase: new PassphraseDTO($task->getPassphrase()->getName())
        );
    }

    public function mapToModel(TaskDto $task, PassphraseEntity $passphrase): TaskEntity
    {
        return (new TaskEntity())
            ->setTitle($task->title)
            ->setDescription($task->description)
            ->setDueDate(new \DateTimeImmutable($task->dueDate))
            ->setPriority($task->priority)
            ->setStatus($task->taskStatus)
            ->setComplete($task->isComplete)
            ->setPassphrase($passphrase);
    }

    public function updateFromDto(TaskEntity $task, TaskDto $taskDTO): TaskEntity
    {
        $task->setTitle($taskDTO->title)
            ->setDescription($taskDTO->description)
            ->setDueDate(new \DateTimeImmutable($taskDTO->dueDate))
            ->setPriority($taskDTO->priority)
            ->setComplete($taskDTO->isComplete)
            ->setStatus($taskDTO->taskStatus);
        return $task;
    }
}
