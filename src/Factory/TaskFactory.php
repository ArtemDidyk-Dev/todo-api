<?php

namespace App\Factory;

use App\Entity\Task;
use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Task::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'created' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'description' => self::faker()->text(),
            'dueDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'passphrase' => null,
            'priority' => self::faker()->randomElement(PriorityEnum::cases()),
            'status' => self::faker()->randomElement(TaskStatusEnum::cases()),
            'title' => self::faker()->text(255),
            'complete' => self::faker()->boolean(),
            'updated' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}
