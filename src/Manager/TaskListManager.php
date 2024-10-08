<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Passphrase;
use App\Repository\TaskRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

final readonly class TaskListManager
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {
    }

    public function getList(Passphrase $passphrase, Request $request): QueryBuilder
    {
        $queryBuilder = $this->taskRepository->getTasks($passphrase);
        $this->applyCompleteFilter($queryBuilder, $request);
        $this->applyStatusFilter($queryBuilder, $request);
        return $queryBuilder;
    }

    private function applyCompleteFilter(QueryBuilder $queryBuilder, Request $request): void
    {
        $isComplete = $request->get('isComplete');
        if (isset($isComplete)) {
            $isComplete = filter_var($isComplete, FILTER_VALIDATE_BOOLEAN);
            $queryBuilder
                ->andWhere('task.isComplete = :isComplete')
                ->setParameter('isComplete', $isComplete);
        }
    }

    private function applyStatusFilter(QueryBuilder $queryBuilder, Request $request): void
    {
        $status = $request->get('status');
        if (isset($status)) {
            $queryBuilder
                ->andWhere('task.status = :status')
                ->setParameter('status', $status);
        }
    }
}
