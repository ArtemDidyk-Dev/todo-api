<?php

declare(strict_types=1);

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
            'description' => self::faker()->text(),
            'dueDate' => (new \DateTimeImmutable('now'))->modify('+'.random_int(1, 27).' days'),
            'passphrase' => null,
            'priority' => self::faker()->randomElement(PriorityEnum::cases()),
            'status' => self::faker()->randomElement(TaskStatusEnum::cases()),
            'title' => self::faker()->text(20),
            'complete' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}
