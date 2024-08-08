<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Serializer\AccessGroup;
use App\Service\PassphraseInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\DTO\Passphrase as PassphraseDTO;
#[Route(path: '/api/v1/passphrase', name: 'passphrase_', methods: ['POST'])]
final class PassphraseController extends AbstractController
{
    public function __construct(
        private readonly PassphraseInterface $passphraseService,
    ) {
    }

    #[Post(
        summary: 'Create passphrase',
        tags: ['Passphrase'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'phrase successfully created',
                content: [
                    new Model(type: PassphraseDTO::class, groups: [AccessGroup::PASSPHRASE_CREATE_RESPONSE, AccessGroup::PASSPHRASE_CREATE]),
                ]
            ),
        ]
    )]
    #[Route('/', name: 'create', format: 'json')]
    public function __invoke(): JsonResponse
    {
        try {
            $passphrase = $this->passphraseService->createPassphrase();
            return $this->json($passphrase, HttpResponse::HTTP_CREATED, [], [
                'groups' => [AccessGroup::PASSPHRASE_CREATE_RESPONSE, AccessGroup::PASSPHRASE_CREATE],
            ]);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_BAD_REQUEST);
        }
    }
}
