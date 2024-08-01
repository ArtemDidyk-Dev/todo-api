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
        #[Groups(AccessGroup::TASKS_READ)]
        public string $title,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Groups(AccessGroup::TASKS_READ)]
        public string $description,

        #[Groups(AccessGroup::TASKS_READ)]
        public ?Passphrase $passphrase = null,

        
        #[Assert\Type('string')]
        #[Groups(AccessGroup::TASKS_READ)]
        public ?string $dueDate = null,

        #[Assert\NotBlank]
        #[Groups(AccessGroup::TASKS_READ)]
        public TaskStatusEnum $taskStatus = TaskStatusEnum::CREATED,

        #[Assert\NotBlank]
        #[Groups(AccessGroup::TASKS_READ)]
        public PriorityEnum $priority = PriorityEnum::Low,


        #[Assert\Type('int')]
        #[Groups(AccessGroup::TASKS_READ)]
        public ?int $id = null,

        #[Assert\Type('bool')]
        #[Groups(AccessGroup::TASKS_READ)]
        public bool $isComplete = false,
    ) {
    }
}
