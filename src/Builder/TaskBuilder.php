<?php

declare(strict_types=1);

namespace App\Builder;


use App\DTO\Task as TaskDto;
use App\DTO\Passphrase as PassphraseDto;
use App\Entity\Passphrase as PassphraseEntity;
use App\Entity\Task as TaskEntity;

final readonly class TaskBuilder
{
    public function mapToDto(TaskEntity $task): TaskDto
    {
        $taskDto = new TaskDto();
        $taskDto->id = $task->getId();
        $taskDto->title = $task->getTitle();
        $taskDto->description = $task->getDescription();
        $taskDto->status = $task->getStatus();
        $taskDto->dueDate = $task->getDueDate();
        $taskDto->priority = $task->getPriority();
        $taskDto->passphrase = (new PassphraseDTO())
            ->setId($task->getPassphrase()->getId())->setPassphrase(
            $task->getPassphrase()->getName()
        );
        $taskDto->isComplete = $task->isComplete();
        return $taskDto;
    }

    public function mapToModel(TaskDto $task, PassphraseEntity $passphrase): TaskEntity
    {

        return (new TaskEntity())
            ->setTitle($task->title)
            ->setDescription($task->description)
            ->setDueDate($task->dueDate)
            ->setPriority($task->priority)
            ->setStatus($task->taskStatus)
            ->setComplete($task->isComplete)
            ->setPassphrase($passphrase);
    }

    public function updateFromDto(TaskEntity $task, TaskDto $taskDTO): TaskEntity
    {
        $task->setTitle($taskDTO->title ?? $task->getTitle());
        $task->setDescription($taskDTO->description ?? $task->getDescription());
        $task->setDueDate($taskDTO->dueDate ?? $task->getDueDate());
        $task->setPriority($taskDTO->priority ?? $task->getPriority());
        $task->setStatus($taskDTO->taskStatus ?? $task->getStatus());
        $task->setComplete($taskDTO->isComplete ?? $task->isComplete());

        return $task;
    }
}
