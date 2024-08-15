<?php

declare(strict_types=1);

namespace App\DTO;


use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use App\Serializer\AccessGroup;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Groups;

 final class Task
{
    #[Groups(AccessGroup::TASK_READ)]
    public int $id;
    #[Groups([AccessGroup::TASK_READ, AccessGroup::TASK_CREATE, AccessGroup::TASK_EDIT])]
    public string $title;

    #[Groups([AccessGroup::TASK_READ, AccessGroup::TASK_CREATE, AccessGroup::TASK_EDIT])]
    public string $description;

    #[Groups([AccessGroup::TASK_READ, AccessGroup::TASK_CREATE])]
    public TaskStatusEnum $taskStatus;

    #[Groups([AccessGroup::TASK_READ, AccessGroup::TASK_CREATE])]
    public PriorityEnum $priority;

    #[Groups(AccessGroup::TASK_READ)]
    public Passphrase $passphrase;

    #[Groups([AccessGroup::TASK_READ, AccessGroup::TASK_CREATE])]
    public ?DateTimeImmutable $dueDate = null;

    #[Groups([AccessGroup::TASK_READ, AccessGroup::TASK_CREATE])]
    public bool $isComplete;


}
