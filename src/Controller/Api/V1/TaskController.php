<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\DTO\Meta;
use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task;
use App\DTO\TaskResponse;
use App\Enum\PriorityEnum;
use App\Enum\TaskStatusEnum;
use App\Serializer\AccessGroup;
use App\Service\TaskServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Examples;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/')]
final class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskServiceInterface $taskService
    ) {
    }
    #[Post(
        summary: 'Crete Task',
        requestBody: new RequestBody(
            description: 'Data for create task.',
            required: true,
            content: [
                new MediaType(
                    mediaType: 'application/json',
                    schema: new Schema(
                        properties: [
                            new Property(
                                property: 'title',
                                title: 'title',
                                type: 'string'
                            ),
                            new Property(
                                property: 'description',
                                title: 'title',
                                type: 'string'
                            ),
                            new Property(
                                property: 'dueDate',
                                title: '2024-08-01 02:24:21',
                                type: 'date'
                            ),
                            new Property(
                                property: 'taskStatus',
                                title: 'created',
                                type: 'string',
                                example: 'created'
                            ),
                            new Property(
                                property: 'priority',
                                title: "1",
                                type: 'integer',
                                example: 1
                            ),
                            new Property(
                                property: 'isComplete',
                                title: 'true',
                                type: 'boolean',
                                example: true,
                            ),

                        ],
                        type: 'object',
                    )
                ),
            ]

        ),
        tags: ['Tasks'],
        parameters: [
            new Parameter(
                name: 'passphrase',
                description: 'The passphrase of the task to update.',
                in: 'query',
                required: true,
            ),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Task update',
                content: [
                    new Model(type: TaskResponse::class, groups: [
                        AccessGroup::TASKS_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('tasks', name: 'tasks_create', methods: 'POST', format: 'json')]
    public function create(
        #[MapQueryString] PassphraseDTO $passphraseDTO,
        #[MapRequestPayload] Task $taskDTO
    ): JsonResponse {
        try {
            $task = $this->taskService->createTask($passphraseDTO, $taskDTO);

            return $this->json([
                'data' => $task,
            ], HttpResponse::HTTP_CREATED);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }

    }

    #[Get(
        summary: 'Get Tasks',
        tags: ['Tasks'],
        parameters: [
            new Parameter(
                name: 'passphrase',
                description: 'The passphrase of the task to delete.',
                in: 'query',
                required: true,
            ),
            new Parameter(
                name: 'isComplete',
                description: 'Filter tasks by complete',
                in: 'query',
                required: false,
                examples: [
                    new Examples(
                        example: "true",
                        summary: 'Task by complete',
                        value: "true"
                    ),
                    new Examples(
                        example: "false",
                        summary: 'Task by not complete',
                        value: "false"
                    ),
                ]
            ),
            new Parameter(
                name: 'status',
                description: 'Filter status by tasks',
                in: 'query',
                required: false,
                examples: [
                    new Examples(
                        example: TaskStatusEnum::CREATED->value,
                        summary: 'Task Status created',
                        value: TaskStatusEnum::CREATED->value
                    ),
                    new Examples(
                        example: TaskStatusEnum::DONE->value,
                        summary: 'Task Status done',
                        value: TaskStatusEnum::DONE->value
                    ),
                    new Examples(
                        example: TaskStatusEnum::FAILED->value,
                        summary: 'Task Status failed',
                        value: TaskStatusEnum::FAILED->value
                    ),
                    new Examples(
                        example: TaskStatusEnum::IN_PROGRESS->value,
                        summary: 'Task Status in progress',
                        value: TaskStatusEnum::IN_PROGRESS->value
                    ),
                ]
            ),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Tasks get',
                content: [new Model(type: TaskResponse::class, groups: [AccessGroup::TASKS_READ])]
            ),
        ]
    )]
    #[Route('tasks', name: 'tasks_index', methods: 'GET', format: 'json')]
    public function index(
        Request $request,
        #[MapQueryString] PassphraseDTO $passphraseDTO,
        #[MapQueryString] Meta $meta
    ): JsonResponse {

        try {
            $tasks = $this->taskService->getTasks($request, $passphraseDTO->passphrase, $meta);
            $data['data'] = [
                'tasks' => $tasks->getItems(),
                'meta' => $tasks->getMeta(),
            ];

            return $this->json($data, HttpResponse::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }
    }

    #[Get(
        summary: 'Get Task',
        tags: ['Tasks'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'The ID of the task to delete.',
                in: 'path',
                required: true,
            ),
            new Parameter(
                name: 'passphrase',
                description: 'The passphrase of the task to delete.',
                in: 'query',
                required: true,
            ),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Task get by id',
                content: [new Model(type: TaskResponse::class, groups: [AccessGroup::TASKS_READ])]
            ),
        ]
    )]
    #[Route('task/{id}', name: 'tasks_show', methods: 'GET', format: 'json')]
    public function show(#[MapQueryString] PassphraseDTO $passphraseDTO, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTask($passphraseDTO, id: $id);

            return $this->json([
                'data' => $task,
            ], HttpResponse::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }

    }

    #[Delete(
        summary: 'Delete Task',
        tags: ['Tasks'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'The ID of the task to delete.',
                in: 'path',
                required: true,
            ),
            new Parameter(
                name: 'passphrase',
                description: 'The passphrase of the task to delete.',
                in: 'query',
                required: true,
            ),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_NO_CONTENT,
                description: 'Task successfully deleted.',
                content: []
            ),
        ]
    )]
    #[Route('task/{id}', name: 'tasks_destroy', methods: 'DELETE', format: 'json')]
    public function destroy(#[MapQueryString] PassphraseDTO $passphraseDTO, int $id): JsonResponse
    {
        try {
            $this->taskService->destroyTask($passphraseDTO, id: $id);

            return $this->json([], HttpResponse::HTTP_NO_CONTENT);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }
    }

    #[Put(
        summary: 'Update Task',
        requestBody: new RequestBody(
            description: 'Data for updating a task.',
            required: true,
            content: [
                new MediaType(
                    mediaType: 'application/json',
                    schema: new Schema(
                        properties: [
                            new Property(
                                property: 'title',
                                title: 'title',
                                type: 'string'
                            ),
                            new Property(
                                property: 'description',
                                title: 'title',
                                type: 'string'
                            ),
                            new Property(
                                property: 'dueDate',
                                title: '2024-08-01 02:24:21',
                                type: 'date'
                            ),
                            new Property(
                                property: 'taskStatus',
                                title: 'created',
                                type: 'string',
                                example: 'created'
                            ),
                            new Property(
                                property: 'priority',
                                title: "1",
                                type: 'integer',
                                example: 1
                            ),
                            new Property(
                                property: 'isComplete',
                                title: 'true',
                                type: 'boolean',
                                example: true,
                            ),

                        ],
                        type: 'object',
                    )
                ),
            ]

        ),
        tags: ['Tasks'],
        parameters: [
            new Parameter(
                name: 'passphrase',
                description: 'The passphrase of the task to update.',
                in: 'query',
                required: true,
            ),
            new Parameter(
                name: 'id',
                description: 'The ID of the task to update.',
                in: 'path',
                required: true,
            ),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Task update',
                content: [
                    new Model(type: TaskResponse::class, groups: [
                        AccessGroup::TASKS_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('task/{id}', name: 'tasks_update', methods: 'PUT', format: 'json')]
    public function update(
        #[MapQueryString] PassphraseDTO $passphraseDTO,
        int $id,
        #[MapRequestPayload] Task $taskDTO
    ): JsonResponse {
        try {
            $task = $this->taskService->updateTask($passphraseDTO, id: $id, taskDTO: $taskDTO);

            return $this->json([
                'data' => $task,
            ], HttpResponse::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }
    }
}
