<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Passphrase as PassphraseDTO;
use App\Entity\Passphrase;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PassphraseService implements PassphraseInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ApiClientInterface $apiClient,
    ) {
    }

    public function createPassphrase(): PassphraseDTO
    {
        $passphraseData = $this->apiClient->get();
        if ($passphraseData) {
            $passphrase = new Passphrase();
            $passphrase->setName($passphraseData['password']);
            $this->entityManager->persist($passphrase);
            $this->entityManager->flush();

            return (new PassphraseDTO())
                ->setId($passphrase->getId())
                ->setPassphrase(
                    $passphrase->getName()
                );
        }
        throw new \InvalidArgumentException('Invalid Passphrase');
    }
}
