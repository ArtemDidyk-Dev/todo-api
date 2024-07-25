<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Task
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $title,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $description,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $dueDate,

        #[Assert\NotBlank]
        public TaskStatusEnum $taskStatus = TaskStatusEnum::CREATED,

        #[Assert\NotBlank]
        public PriorityEnum $priority = PriorityEnum::Low,

        #[Assert\Type('int')]
        public ?int $id = null,

        #[Assert\Type('bool')]
        public bool $isComplete = false,

        public ?Passphrase $passphrase = null,
    ) {
    }
}
