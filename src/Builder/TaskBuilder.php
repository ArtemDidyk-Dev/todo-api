<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task as TaskDto;
use App\DTO\TaskResponse as TaskResponseDTO;
use App\Entity\Passphrase as PassphraseEntity;
use App\Entity\Task as TaskEntity;
use App\DTO\PassphraseResponse as PassphraseResponseDTO;
class TaskBuilder
{
    public function mapToDto(TaskEntity $task): TaskResponseDTO
    {
        return new TaskResponseDTO(
            id: $task->getId(),
            title: $task->getTitle(),
            description: $task->getDescription(),
            passphrase: new PassphraseResponseDTO($task->getPassphrase()->getId(), $task->getPassphrase()->getName()),
            taskStatus: $task->getStatus(),
            dueDate: $task->getDueDate()?->format('Y-m-d H:i:s'),
            priority: $task->getPriority(),
            isComplete: $task->isComplete()
        );
    }


    public function mapToModel(TaskDto $task, PassphraseEntity $passphrase): TaskEntity
    {
        $dueDate = $task->dueDate ? new \DateTimeImmutable($task->dueDate) : null;
        return (new TaskEntity())
            ->setTitle($task->title)
            ->setDescription($task->description)
            ->setDueDate( $dueDate )
            ->setPriority($task->priority)
            ->setStatus($task->taskStatus)
            ->setComplete($task->isComplete)
            ->setPassphrase($passphrase);
    }

    public function updateFromDto(TaskEntity $task, TaskDto $taskDTO): TaskEntity
    {
        $dueDate = $taskDTO->dueDate ? new \DateTimeImmutable($taskDTO->dueDate) : null;
        $task->setTitle($taskDTO->title)
            ->setDescription($taskDTO->description)
            ->setDueDate($dueDate)
            ->setPriority($taskDTO->priority)
            ->setComplete($taskDTO->isComplete)
            ->setStatus($taskDTO->taskStatus);
        return $task;
    }
}
