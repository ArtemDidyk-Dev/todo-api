<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Service\PassphraseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/passphrase', name: 'passphrase_', methods: ['POST'])]
class PassphraseController extends AbstractController
{
    public function __construct(
        private readonly PassphraseInterface $passphraseService,
    ) {
    }

    #[Route('/', name: 'create', format: 'json')]
    public function __invoke(): JsonResponse
    {
        try {
            $passphrase = $this->passphraseService->createPassphrase();
            $data = [
                'massage' => 'phrase successfully created',
                'passphrase' => $passphrase,
            ];
            return $this->json($data, Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
