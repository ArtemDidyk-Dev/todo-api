<?php

declare(strict_types=1);

namespace App\Service;

use App\Builder\TaskBuilder;
use App\DTO\Meta;
use Iterator;
use ReturnTypeWillChange;

final class TaskList implements Iterator
{
    public array $data = [];

    public Meta $meta;

    private int $position = 0;

    public function __construct(
        private readonly TaskBuilder $taskBuilder,
    ) {

    }

    public function addTasks(array $tasks): self
    {
        foreach ($tasks as $task) {
           $this->addItem($this->taskBuilder->mapToDto($task));
        }
        return $this;
    }

    public function addMeta(int $currentPageNumber, int $totalItemCount, int $itemNumberPerPage): self
    {
        $this->meta = new Meta(
            $currentPageNumber,
            $itemNumberPerPage,
            $totalItemCount,
            (int) ceil($totalItemCount / $itemNumberPerPage)
        );
        return $this;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function addItem($item): void
    {
        $this->data[] = $item;
    }

    public function getItems(): array
    {
        return $this->data;
    }

    #[ReturnTypeWillChange]
    public function current(): mixed
    {
        return $this->data[$this->position];
    }

    #[ReturnTypeWillChange]
    public function next(): void
    {
        $this->position++;
    }

    #[ReturnTypeWillChange]
    public function key(): int
    {
        return $this->position;
    }

    #[ReturnTypeWillChange]
    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }

    #[ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->position = 0;
    }
}
