<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use App\Serializer\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Task
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Groups([AccessGroup::TASKS_READ, AccessGroup::TASKS_CREATE])]
        public string $title,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Groups([AccessGroup::TASKS_READ, AccessGroup::TASKS_CREATE])]
        public string $description,

        #[Groups(AccessGroup::TASKS_READ)]
        public ?int $id = null,

        #[Groups(AccessGroup::TASKS_READ)]
        public ?Passphrase $passphrase = null,

        #[Assert\Type('string')]
        #[Groups([AccessGroup::TASKS_READ, AccessGroup::TASKS_CREATE])]
        public ?string $dueDate = null,

        #[Assert\NotBlank]
        #[Groups([AccessGroup::TASKS_READ, AccessGroup::TASKS_CREATE])]
        public TaskStatusEnum $taskStatus = TaskStatusEnum::CREATED,

        #[Assert\NotBlank]
        #[Groups([AccessGroup::TASKS_READ, AccessGroup::TASKS_CREATE])]
        public PriorityEnum $priority = PriorityEnum::Low,

        #[Assert\Type('bool')]
        #[Groups([AccessGroup::TASKS_READ, AccessGroup::TASKS_CREATE])]
        public bool $isComplete = false,
    ) {
    }
}
