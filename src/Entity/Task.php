<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use App\Listener\TaskListener;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\EntityListeners([TaskListener::class])]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Index(columns: ['title'], name: 'task_title_idx')]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $title;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[Assert\DateTime]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $dueDate = null;

    #[ORM\Column(type: 'integer', enumType: PriorityEnum::class)]
    #[Assert\Type('integer')]
    private PriorityEnum $priority;
    #[ORM\Column(type: 'string', enumType: TaskStatusEnum::class)]
    #[Assert\Type('string')]
    private TaskStatusEnum $status;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Passphrase $passphrase;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isComplete;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $created;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updated;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $expiring = false;

    public function __construct()
    {
        $this->status = TaskStatusEnum::CREATED;
        $this->priority = PriorityEnum::Low;
        $this->isComplete = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getPriority(): PriorityEnum
    {
        return $this->priority;
    }

    public function setPriority(PriorityEnum $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPassphrase(): Passphrase
    {
        return $this->passphrase;
    }

    public function setPassphrase(Passphrase $passphrase): self
    {
        $this->passphrase = $passphrase;

        return $this;
    }

    public function isComplete(): bool
    {
        return $this->isComplete;
    }

    public function setComplete(bool $isComplete): self
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    public function getUpdated(): DateTimeImmutable
    {
        return $this->updated;
    }

    public function setUpdated(DateTimeImmutable $updated): Task
    {
        $this->updated = $updated;

        return $this;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(DateTimeImmutable $created): Task
    {
        $this->created = $created;

        return $this;
    }

    public function getStatus(): TaskStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TaskStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isExpiring(): bool
    {
        return $this->expiring;
    }

    public function setExpiring(bool $expiring): Task
    {
        $this->expiring = $expiring;

        return $this;
    }
}
