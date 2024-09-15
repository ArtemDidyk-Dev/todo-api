<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Task;
use App\Enum\TaskStatusEnum;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final readonly class TaskStatusExpiringUpdater
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    public function update(Task $entity): void
    {
        $oldExpiringStatus = $entity->isExpiring();
        $dueDate = $entity->getDueDate();
        $now = new DateTimeImmutable('now');
        if ($dueDate === null || $now < $dueDate->modify('- 3 days')) {
            $newExpiringStatus = false;
        } else {
            $newExpiringStatus = true;
        }
        if($oldExpiringStatus !== $newExpiringStatus) {
            $entity->setExpiring($newExpiringStatus);
            $entity->setStatus(TaskStatusEnum::FAILED);
        }
    }

    public function updateAll(): void
    {
        $repository = $this->em->getRepository(Task::class);
        $tasks = $repository->findAll();
        foreach ($tasks as $task) {
            $this->update($task);
            $this->em->flush();
        }
    }
}

