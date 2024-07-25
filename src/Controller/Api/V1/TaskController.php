<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\DTO\Meta;
use App\DTO\Passphrase;
use App\DTO\Task;
use App\Service\TaskInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/')]
final class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskInterface $task
    ) {
    }

    #[Route('tasks', name: 'tasks_create', methods: 'POST', format: 'json')]
    public function create(
        #[MapQueryString] Passphrase $passphraseDTO,
        #[MapRequestPayload] Task $taskDTO
    ): JsonResponse {
        try {
            $task = $this->task->createTask($passphraseDTO, $taskDTO);
            return $this->json([
                'data' => $task,
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    #[Route('tasks', name: 'tasks_index', methods: 'GET', format: 'json')]
    public function index(#[MapQueryString] Passphrase $passphraseDTO, #[MapQueryString] Meta $meta): JsonResponse
    {

        try {
            $tasks = $this->task->getTasks($passphraseDTO->passphrase, $meta);
            $data['data'] = [
                'tasks' => $tasks->getItems(),
                'meta' => $tasks->getMeta()
            ];
            return $this->json($data, Response::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('task/{id}', name: 'tasks_show', methods: 'GET', format: 'json')]
    public function show(#[MapQueryString] Passphrase $passphraseDTO, int $id): JsonResponse
    {
        try {
            $task = $this->task->getTask($passphraseDTO, id: $id);
            return $this->json([
                'data' => $task,
            ], Response::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    #[Route('task/{id}', name: 'tasks_destroy', methods: 'DELETE', format: 'json')]
    public function destroy(#[MapQueryString] Passphrase $passphraseDTO, int $id): JsonResponse
    {
        try {
            $this->task->destroyTask($passphraseDTO, id: $id);
            return $this->json([], Response::HTTP_NO_CONTENT);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('task/{id}', name: 'tasks_update', methods: 'PUT', format: 'json')]
    public function update(
        #[MapQueryString] Passphrase $passphraseDTO,
        int $id,
        #[MapRequestPayload] Task $taskDTO
    ): JsonResponse {
        try {
            $task = $this->task->updateTask($passphraseDTO, id: $id, taskDTO: $taskDTO);
            return $this->json([
                'data' => $task,
            ], Response::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
