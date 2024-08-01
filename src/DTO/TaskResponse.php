<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use App\Serializer\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class TaskResponse
{
    public function __construct(

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Groups(AccessGroup::TASKS_READ)]
        public int $id,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Groups(AccessGroup::TASKS_READ)]
        public string $title,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Groups(AccessGroup::TASKS_READ)]
        public string $description,

        #[Groups(AccessGroup::TASKS_READ)]
        public PassphraseResponse $passphrase,

        #[Assert\NotBlank]
        #[Groups(AccessGroup::TASKS_READ)]
        public TaskStatusEnum $taskStatus = TaskStatusEnum::CREATED,

        #[Assert\Type('string')]
        #[Groups(AccessGroup::TASKS_READ)]
        public ?string $dueDate = null,

        #[Assert\NotBlank]
        #[Groups(AccessGroup::TASKS_READ)]
        public PriorityEnum $priority = PriorityEnum::Low,


        #[Assert\Type('bool')]
        #[Groups(AccessGroup::TASKS_READ)]
        public bool $isComplete = false
    ) {
    }
}
