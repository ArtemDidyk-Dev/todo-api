<?php

namespace App\Listener;

use App\Entity\Task;
use App\Service\TaskStatusExpiringUpdater;
use DateTimeImmutable;

final readonly class TaskListener
{
    public function __construct(
        private TaskStatusExpiringUpdater $expiringUpdater
    ) {
    }

    public function prePersist(Task $entity): void
    {
        $entity->setCreated(new DateTimeImmutable());
        $entity->setUpdated(new DateTimeImmutable());
    }

    public function preUpdate(Task $entity): void
    {
        $entity->setUpdated(new DateTimeImmutable());
    }

    public function postLoad(Task $entity): void
    {
        $this->expiringUpdater->update($entity);
    }
}
