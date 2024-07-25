<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PassphraseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PassphraseRepository::class)]
#[ORM\Index(columns: ['name'], name: 'passphrase_name_idx')]
class Passphrase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Unique]
    #[Assert\Type('string')]
    private string $name;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(mappedBy: 'passphrase', targetEntity: Task::class, cascade: [
        'persist',
        'remove',
    ], orphanRemoval: true)]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (! $this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setPassphrase($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        // set the owning side to null (unless already changed)
        if ($this->tasks->removeElement($task) && $task->getPassphrase() === $this) {
            $task->setPassphrase(null);
        }

        return $this;
    }
}
