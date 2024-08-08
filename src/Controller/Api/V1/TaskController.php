<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\DTO\Meta;
use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task;
use App\Enum\TaskStatusEnum;
use App\Serializer\AccessGroup;
use App\Service\ExcelExportService;
use App\Service\TaskServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Examples;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
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
        private readonly TaskServiceInterface $taskService,
        private readonly ExcelExportService $excelExportService
    ) {
    }

    #[Post(
        summary: 'Crete Task',
        requestBody: new RequestBody(
            description: 'Data for create task.',
            required: true,
            content: new JsonContent(
                ref: new Model(
                    type: Task::class,
                    groups: [AccessGroup::TASKS_CREATE]
                )
            )
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
                    new Model(type: Task::class, groups: [
                        AccessGroup::TASKS_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('tasks', name: 'tasks_create', methods: 'POST')]
    public function create(
        #[MapQueryString(
            serializationContext: ['groups' => [AccessGroup::PASSPHRASE_CREATE_RESPONSE]],
        )]
        PassphraseDTO $passphraseDTO,
        #[MapRequestPayload(
            serializationContext: ['groups' => [AccessGroup::TASKS_CREATE]],
            validationGroups: [AccessGroup::TASKS_CREATE]
        )]
        Task $taskDTO
    ): JsonResponse {

        try {
            $task = $this->taskService->createTask($passphraseDTO, $taskDTO);

            return $this->json([
                'data' => $task,
            ], HttpResponse::HTTP_CREATED, [], [
                'groups' => [AccessGroup::TASKS_READ, AccessGroup::PASSPHRASE_CREATE],
            ]);

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
                description: 'The passphrase of the task',
                in: 'query',
                required: true,
            ),

            new Parameter(
                name: 'isComplete',
                description: 'Filter tasks by complete',
                in: 'query',
                required: false,
                examples: [
                    new Examples(example: 'true', summary: 'Task by complete', value: 'true'),
                    new Examples(example: 'false', summary: 'Task by not complete', value: 'false'),
                ]
            ),
            new Parameter(
                name: 'status',
                description: 'Filter by task status',
                in: 'query',
                required: false,
                schema: new Schema(
                    type: 'string',
                    enum: [
                        TaskStatusEnum::DONE,
                        TaskStatusEnum::FAILED,
                        TaskStatusEnum::CREATED,
                        TaskStatusEnum::IN_PROGRESS,
                    ]
                )
            ),

        ],

        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Tasks get',
                content: [
                    new Model(type: Task::class, groups: [
                        AccessGroup::TASKS_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('tasks', name: 'tasks_index', methods: 'GET', format: 'json')]
    public function index(
        Request $request,
        #[MapQueryString(
            serializationContext: ['groups' => [AccessGroup::PASSPHRASE_CREATE_RESPONSE]],
        )]
        PassphraseDTO $passphraseDTO,
        #[MapQueryString]
        Meta $meta
    ): JsonResponse {

        try {

            $tasks = $this->taskService->getTasks($request, $passphraseDTO->passphrase, $meta);
            $data['data'] = [
                'tasks' => $tasks->getItems(),
                'meta' => $tasks->getMeta(),
            ];

            return $this->json(
                $data
                ,
                HttpResponse::HTTP_CREATED, [],
                [
                    'groups' => [AccessGroup::TASKS_READ, AccessGroup::PASSPHRASE_CREATE],
                ]
            );

        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }
    }

    #[Get(
        summary: 'Get Task',
        tags: ['Tasks'],
        parameters: [
            new Parameter(name: 'id', description: 'The ID of the task to delete.', in: 'path', required: true),
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
                content: [new Model(type: Task::class, groups: [AccessGroup::TASKS_READ])]
            ),
        ]
    )]
    #[Route('task/{id}', name: 'tasks_show', methods: 'GET', format: 'json')]
    public function show(
        #[MapQueryString(
            serializationContext: ['groups' => [AccessGroup::PASSPHRASE_CREATE_RESPONSE]],
        )]
        PassphraseDTO $passphraseDTO,
        int $id
    ): JsonResponse {
        try {
            $task = $this->taskService->getTask($passphraseDTO, id: $id);

            return $this->json([
                'data' => $task,
            ], HttpResponse::HTTP_CREATED, [], [
                'groups' => [AccessGroup::TASKS_READ, AccessGroup::PASSPHRASE_CREATE],
            ]);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }

    }

    #[Delete(
        summary: 'Delete Task',
        tags: ['Tasks'],
        parameters: [
            new Parameter(name: 'id', description: 'The ID of the task to delete.', in: 'path', required: true),
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
    public function destroy(
        #[MapQueryString(
            serializationContext: ['groups' => [AccessGroup::PASSPHRASE_CREATE_RESPONSE]],
        )]
        PassphraseDTO $passphraseDTO,
        int $id
    ): JsonResponse {
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
            description: 'Data for create task.',
            required: true,
            content: new JsonContent(
                ref: new Model(
                    type: Task::class,
                    groups: [AccessGroup::TASKS_CREATE]
                )
            )
        ),
        tags: ['Tasks'],
        parameters: [
            new Parameter(
                name: 'passphrase',
                description: 'The passphrase of the task to update.',
                in: 'query',
                required: true,
            ),
            new Parameter(name: 'id', description: 'The ID of the task to update.', in: 'path', required: true),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Task update',
                content: [
                    new Model(type: Task::class, groups: [
                        AccessGroup::TASKS_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('task/{id}', name: 'tasks_update', methods: 'PUT', format: 'json')]


    public function update(
        #[MapQueryString(
            serializationContext: ['groups' => [AccessGroup::PASSPHRASE_CREATE_RESPONSE]],
        )]
        PassphraseDTO $passphraseDTO,
        int $id,
        #[MapRequestPayload(
            serializationContext: ['groups' => [AccessGroup::TASKS_CREATE]],
            validationGroups: [AccessGroup::TASKS_CREATE]
        )]
        Task $taskDTO
    ): JsonResponse {
        try {
            $task = $this->taskService->updateTask($passphraseDTO, id: $id, taskDTO: $taskDTO);

            return $this->json([
                'data' => $task,
            ], HttpResponse::HTTP_CREATED, [], [
                'groups' => [AccessGroup::TASKS_READ, AccessGroup::PASSPHRASE_CREATE],
            ]);

        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }
    }

    #[Route('export', name: 'tasks_export', methods: 'GET', format: 'json')]
    public function export(#[MapQueryString] PassphraseDTO $passphraseDTO): HttpResponse
    {
        return $this->excelExportService->export($passphraseDTO->passphrase);
    }
}
