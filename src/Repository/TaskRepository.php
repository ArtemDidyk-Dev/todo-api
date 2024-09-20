<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Passphrase;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getPassphraseTaskId(string $passphrase, int $id): ?Task
    {

        return $this->createQueryBuilder('p')
            ->innerJoin('p.passphrase', 't')
            ->andWhere('t.name = :passphraseName')
            ->andWhere('p.id = :taskId')
            ->setParameter('passphraseName', $passphrase)
            ->setParameter('taskId', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTasks(Passphrase $passphrase): QueryBuilder
    {
        return $this->createQueryBuilder('task')
            ->andWhere('task.passphrase = :passphrase')
            ->setParameter('passphrase', $passphrase);
    }

    public function getAll(string $passphrase): array
    {

        return $this->createQueryBuilder('p')
            ->innerJoin('p.passphrase', 't')
            ->andWhere('t.name = :passphraseName')
            ->setParameter('passphraseName', $passphrase)
            ->getQuery()
            ->getResult();

    }
}
