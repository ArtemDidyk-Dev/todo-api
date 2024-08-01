<?php

declare(strict_types=1);

namespace App\Entity;

use AllowDynamicProperties;
use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Index(columns: ['title'], name: 'task_title_idx')]
#[HasLifecycleCallbacks]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $title;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $dueDate = null;

    #[ORM\Column(type: 'integer', enumType: PriorityEnum::class)]
    #[Assert\Type('integer')]
    private PriorityEnum $priority = PriorityEnum::Low;

    #[ORM\Column(type: 'string', enumType: TaskStatusEnum::class)]
    #[Assert\Type('string')]
    private TaskStatusEnum $status = TaskStatusEnum::CREATED;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Passphrase $passphrase;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isComplete = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $created;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updated;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setDueDate(DateTimeImmutable $dueDate): self
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

    public function getCreated(): ?DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(?DateTimeImmutable $created): self
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTimeImmutable
    {
        return $this->updated;
    }

    public function setUpdated(?DateTimeImmutable $updated): self
    {
        $this->updated = $updated;
        return $this;
    }

    public function getStatus(): TaskStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TaskStatusEnum $status): Task
    {
        $this->status = $status;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->created = new DateTimeImmutable();
        $this->updated = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updated = new DateTimeImmutable();
    }

}
