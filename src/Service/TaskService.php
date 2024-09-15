<?php

declare(strict_types=1);

namespace App\Service;

use App\Builder\TaskBuilder;
use App\DTO\Meta;
use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task as TaskDto;
use App\Entity\Task as TaskEntity;
use App\Manager\TaskListManager;
use App\Repository\PassphraseRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;


final readonly class TaskService implements TaskServiceInterface
{
    public function __construct(
        private PassphraseRepository $passphraseRepository,
        private EntityManagerInterface $em,
        private TaskRepository $taskRepository,
        private TaskBuilder $taskBuilder,
        private PaginatorInterface $paginator,
        private TaskListManager $taskListManager,
        private TaskStatusExpiringUpdater $taskStatusExpiringUpdater,
    ) {
    }

    public function createTask(PassphraseDTO $passphraseDTO, TaskDTO $taskDTO): TaskDto
    {

        $passphrase = $this->passphraseRepository->findOneBy([
            'name' => $passphraseDTO->passphrase,
        ]);
        if ($passphrase === null) {
            throw new \InvalidArgumentException('Passphrase not found');
        }
        $task = $this->taskBuilder->mapToModel($taskDTO, $passphrase);
        $this->em->persist($task);
        $this->em->flush();
        return $this->taskBuilder->mapToDto($task);

    }

    public function getTasks(Request $request, string $passphrase, Meta $meta): TaskList
    {
        $passphraseData = $this->passphraseRepository->findOneBy([
            'name' => $passphrase,
        ]);
        if ($passphraseData === null) {
            throw new \InvalidArgumentException('Passphrase not found');
        }
        $tasks = $this->taskListManager->getList($passphraseData->getId(), $request);
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
     * @param string $passphrase
     * @return TaskDto[]
     */
    public function getAll(string $passphrase): array
    {
        /** @var TaskDto[] $tasks */
        $tasks = $this->taskRepository->getAll($passphrase);
        $taskDTOs = [];
        /** @var TaskEntity $task */
        foreach ($tasks as $task) {
            $this->taskStatusExpiringUpdater->update($task);
            $taskDTOs[] = $this->taskBuilder->mapToDto($task);
        }
        $this->em->flush();

        return  $taskDTOs;
    }

    public function getTask(PassphraseDTO $passphraseDTO, int $id): TaskDto
    {
        $taskEntity = $this->taskRepository->getPassphraseTaskId($passphraseDTO->passphrase, $id);
        if ($taskEntity === null) {
            throw new \InvalidArgumentException('Task not found');
        }
        $this->taskStatusExpiringUpdater->update($taskEntity);
        $this->em->flush();
        return $this->taskBuilder->mapToDto($taskEntity);
    }

    public function destroyTask(PassphraseDTO $passphraseDTO, int $id): void
    {
        $data = $this->taskRepository->getPassphraseTaskId($passphraseDTO->passphrase, $id);
        if ($data === null) {
            throw new \InvalidArgumentException('Task not found');
        }
        $this->em->remove($data);
        $this->em->flush();
    }

    public function updateTask(PassphraseDTO $passphraseDTO, TaskEntity $task, TaskDTO $taskDTO): TaskDto
    {
        if($passphraseDTO->passphrase !== $task->getPassphrase()->getName()) {
            throw new BadRequestException('bad passphrase');
        }
        $taskUpdated = $this->taskBuilder->updateFromDto($task, $taskDTO);
        $this->em->flush();
        return $this->taskBuilder->mapToDto($taskUpdated);
    }
}
