<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Passphrase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Passphrase>
 */
class PassphraseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Passphrase::class);
    }

    public function getPassphrase(string $passphrase): JsonResponse|Passphrase
    {
        if ($this->findOneBy([
            'name' => $passphrase,
        ])) {
            return $this->findOneBy([
                'name' => $passphrase,
            ]);
        }
        return new JsonResponse([
            'error' => 'Passphrase not found',
            'data' => [],
        ], Response::HTTP_NOT_FOUND);
    }
}
