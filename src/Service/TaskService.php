<?php

declare(strict_types=1);

namespace App\Service;

use App\Builder\TaskBuilder;
use App\DTO\Meta;
use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task as TaskDTO;
use App\Repository\PassphraseRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

final readonly class TaskService implements TaskInterface
{
    // TaskServiceInterface
    public function __construct(
        private PassphraseRepository $passphraseRepository,
        private EntityManagerInterface $manager,
        private TaskRepository $taskRepository,
        private TaskBuilder $taskBuilder,
        private PaginatorInterface $paginator,
    ) {
    }

    public function createTask(PassphraseDTO $passphraseDTO, TaskDTO $taskDTO): TaskDTO
    {
        $passphrase = $this->passphraseRepository->findOneBy([
            'name' => $passphraseDTO->passphrase,
        ]);
        if ($passphrase === null) {
            throw new \InvalidArgumentException('Passphrase not found');
        }
        $task = $this->taskBuilder->mapToModel($taskDTO, $passphrase);
        $this->manager->persist($task);
        $this->manager->flush();
        return $this->taskBuilder->mapToDto($task);

    }

    public function getTasks(string $passphrase, Meta $meta): TaskList
    {

        $passphraseData = $this->passphraseRepository->findOneBy([
            'name' => $passphrase,
        ]);
        if ($passphraseData === null) {
            throw new \InvalidArgumentException('Passphrase not found');
        }
        $tasks = $this->taskRepository->getTasks($passphraseData->getId());
        $paginator = $this->paginator->paginate($tasks, $meta->currentPage, $meta->itemsPerPage);
        return (new TaskList($this->taskBuilder))
            ->addTasks($paginator->getItems())
            ->addMeta(
                $paginator->getCurrentPageNumber(),
                $paginator->getTotalItemCount(),
                $paginator->getItemNumberPerPage()
            );
    }

    public function getTask(PassphraseDTO $passphraseDTO, int $id): TaskDTO
    {
        $data = $this->taskRepository->getPassphraseTaskId($passphraseDTO->passphrase, $id);
        if ($data === null) {
            throw new \InvalidArgumentException('Task not found');
        }

        return $this->taskBuilder->mapToDto($data);
    }

    public function destroyTask(PassphraseDTO $passphraseDTO, int $id): void
    {
        $data = $this->taskRepository->getPassphraseTaskId($passphraseDTO->passphrase, $id);
        if ($data === null) {
            throw new \InvalidArgumentException('Task not found');
        }
        $this->manager->remove($data);
        $this->manager->flush();
    }

    public function updateTask(PassphraseDTO $passphraseDTO, int $id, TaskDTO $taskDTO): TaskDTO
    {
        $data = $this->taskRepository->getPassphraseTaskId($passphraseDTO->passphrase, $id);
        if ($data === null) {
            throw new \InvalidArgumentException('Task not found');
        }
        $task = $this->taskBuilder->updateFromDto($data, $taskDTO);
        $this->manager->flush($task);

        return $this->taskBuilder->mapToDto($task);
    }
}
