<?php

declare(strict_types=1);

namespace App\Service;

use App\Builder\TaskBuilder;
use App\DTO\Meta;
use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task as TaskDto;
use App\Entity\Passphrase;
use App\Entity\Task as TaskEntity;
use App\Exception\BadPassphraseException;
use App\Exception\NotFoundException;
use App\Manager\TaskListManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;


final readonly class TaskService implements TaskServiceInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TaskBuilder $taskBuilder,
        private PaginatorInterface $paginator,
        private TaskListManager $taskListManager,
        private TaskStatusExpiringUpdater $taskStatusExpiringUpdater,
        private LoggerInterface $logger,
    ) {
    }


    public function createTask(Passphrase $passphrase, TaskDTO $taskDTO): TaskDto
    {
        $task = $this->taskBuilder->mapToModel($taskDTO, $passphrase);
        $this->em->persist($task);
        $this->em->flush();
        return $this->taskBuilder->mapToDto($task);
    }

    /**
     * @throws NotFoundException
     */
    public function getTasks(Request $request, Passphrase $passphrase, Meta $meta): TaskList
    {

        $tasks = $this->taskListManager->getList($passphrase, $request);
        $paginator = $this->paginator->paginate($tasks, $meta->currentPage, $meta->itemsPerPage);
        return (new TaskList($this->taskBuilder))
            ->addTasks($paginator->getItems())
            ->addMeta(
                $paginator->getCurrentPageNumber(),
                $paginator->getTotalItemCount(),
                $paginator->getItemNumberPerPage()
            );
    }

    /**
     * @param Passphrase $passphrase
     * @return TaskDto[]
     */
    public function getAll(Passphrase $passphrase): array
    {
        /** @var TaskDto[] $tasks */
        $tasks = $passphrase->getTasks();
        $taskDTOs = [];
        /** @var TaskEntity $task */
        foreach ($tasks as $task) {
            $this->taskStatusExpiringUpdater->update($task);
            $taskDTOs[] = $this->taskBuilder->mapToDto($task);
        }
        $this->em->flush();

        return  $taskDTOs;
    }

    /**
     * @throws NotFoundException
     */
    public function getTask(Passphrase $passphrase, TaskEntity $task): TaskDto
    {
        if (!$passphrase->getTasks()->contains($task)) {
            $context = [
                'passphrase' => $passphrase->getId(),
                'task_id' => $task->getId(),
            ];
            $message = 'Resource not found';
            $this->logger->error($message, $context);
            throw new NotFoundException();
        }
        $this->taskStatusExpiringUpdater->update($task);
        $this->em->flush();
        return $this->taskBuilder->mapToDto($task);
    }

    /**
     * @throws NotFoundException
     */
    public function destroyTask(Passphrase $passphrase, TaskEntity $task): void
    {
        if (!$passphrase->getTasks()->contains($task)) {
            $context = [
                'passphrase' => $passphrase->getId(),
                'task_id' => $task->getId(),
            ];
            $message = 'Resource not found';
            $this->logger->error($message, $context);
            throw new NotFoundException();
        }
        $this->em->remove($task);
        $this->em->flush();
    }

    /**
     * @throws BadPassphraseException
     */
    public function updateTask(PassphraseDTO $passphraseDTO, TaskEntity $task, TaskDTO $taskDTO): TaskDto
    {
        if($passphraseDTO->passphrase !== $task->getPassphrase()->getName()) {
            $context = [
                'passphrase' => $passphraseDTO->passphrase,
                'task_id' => $task->getId(),
            ];
            $message = 'Bad Passphrase';
            $this->logger->error($message, $context);
            throw new BadPassphraseException($message);
        }
        $taskUpdated = $this->taskBuilder->updateFromDto($task, $taskDTO);
        $this->em->flush();
        return $this->taskBuilder->mapToDto($taskUpdated);
    }
}
