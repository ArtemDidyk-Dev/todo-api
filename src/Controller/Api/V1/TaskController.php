<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\DTO\Export;
use App\DTO\Meta;
use App\DTO\Passphrase as PassphraseDTO;
use App\DTO\Task;
use App\Entity\Passphrase;
use App\Entity\Task as TaskEntity;
use App\Enum\ExportEnum;
use App\Enum\TaskStatusEnum;
use App\Serializer\AccessGroup;
use App\Service\ExportFactory;
use App\Service\TaskServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Examples;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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
        private readonly ExportFactory $exportFactory,
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
                    groups: [AccessGroup::TASK_CREATE]
                )
            )
        ),
        tags: ['Tasks'],

        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Task update',
                content: [
                    new Model(type: Task::class, groups: [
                        AccessGroup::TASK_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('passphrase/{id}/tasks', name: 'tasks_create', methods: 'POST')]
    public function create(
        #[MapEntity]
        Passphrase $passphrase,
        #[MapRequestPayload(
            serializationContext: ['groups' => [AccessGroup::TASK_CREATE]],
            validationGroups: [AccessGroup::TASK_CREATE]
        )]
        Task $taskDTO
    ): JsonResponse {

        $task = $this->taskService->createTask($passphrase, $taskDTO);
        return $this->json([
            'data' => $task,
        ], HttpResponse::HTTP_CREATED, [], [
            'groups' => [AccessGroup::TASK_READ, AccessGroup::PASSPHRASE_CREATE],
        ]);
    }

    #[Get(
        summary: 'Get Tasks',
        tags: ['Tasks'],
        parameters: [
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
                        AccessGroup::TASK_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('passphrase/{id}/tasks', name: 'tasks_index', methods: 'GET', format: 'json')]
    public function index(
        Request $request,
        #[MapEntity]
        Passphrase $passphrase,
        #[MapQueryString]
        Meta $meta
    ): JsonResponse {

        $tasks = $this->taskService->getTasks($request, $passphrase, $meta);
        $data['data'] = [
            'tasks' => $tasks->getItems(),
            'meta' => $tasks->getMeta(),
        ];
        return $this->json(
            $data
            ,
            HttpResponse::HTTP_CREATED, [],
            [
                'groups' => [AccessGroup::TASK_READ, AccessGroup::PASSPHRASE_CREATE],
            ]
        );
    }

    #[Get(
        summary: 'Get Task',
        tags: ['Tasks'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Task get by id',
                content: [new Model(type: Task::class, groups: [AccessGroup::TASK_READ])]
            ),
        ]
    )]
    #[Route('passphrase/{passphraseId}/task/{taskId}', name: 'tasks_show', methods: ['GET'], format: 'json')]
    public function show(
        #[MapEntity(id: 'passphraseId')] Passphrase $passphrase,
        #[MapEntity(id: 'taskId')] TaskEntity $task
    ): JsonResponse {
        $task = $this->taskService->getTask(passphrase: $passphrase, task: $task);
        return $this->json([
            'data' => $task,
        ], HttpResponse::HTTP_CREATED, [], [
            'groups' => [AccessGroup::TASK_READ, AccessGroup::PASSPHRASE_CREATE],
        ]);
    }

    #[Delete(
        summary: 'Delete Task',
        tags: ['Tasks'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_NO_CONTENT,
                description: 'Task successfully deleted.',
                content: []
            ),
        ]
    )]
    #[Route('passphrase/{passphraseId}/task/{taskId}', name: 'tasks_destroy', methods: 'DELETE', format: 'json')]
    public function destroy(
        #[MapEntity(id: 'passphraseId')] Passphrase $passphrase,
        #[MapEntity(id: 'taskId')] TaskEntity $task
    ): JsonResponse {
        $this->taskService->destroyTask(passphrase: $passphrase, task: $task);
        return $this->json([], HttpResponse::HTTP_NO_CONTENT);
    }

    #[Patch(
        summary: 'Update Task',
        requestBody: new RequestBody(
            description: 'Data for create task.',
            required: true,
            content: new JsonContent(
                ref: new Model(
                    type: Task::class,
                    groups: [AccessGroup::TASK_CREATE]
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
                        AccessGroup::TASK_READ,
                        AccessGroup::PASSPHRASE_CREATE_RESPONSE,
                    ]),
                ]
            ),
        ]
    )]
    #[Route('task/{id}', name: 'tasks_update', methods: 'PATCH', format: 'json')]
    public function update(
        #[MapQueryString(
            serializationContext: ['groups' => [AccessGroup::PASSPHRASE_CREATE_RESPONSE]],
        )]
        PassphraseDTO $passphraseDTO,
        TaskEntity $task,
        #[MapRequestPayload(
            serializationContext: ['groups' => [AccessGroup::TASK_EDIT]],
            validationGroups: [AccessGroup::TASK_EDIT]
        )]
        Task $taskDTO,
    ): JsonResponse {
        $task = $this->taskService->updateTask($passphraseDTO, $task, $taskDTO);

        return $this->json([
            'data' => $task,
        ], HttpResponse::HTTP_CREATED, [], [
            'groups' => [AccessGroup::TASK_READ, AccessGroup::PASSPHRASE_CREATE],
        ]);
    }

    #[Get(
        summary: 'Export Tasks',
        tags: ['Tasks'],
        parameters: [
            new Parameter(
                name: 'type',
                description: 'Type export tasks',
                in: 'query',
                required: false,
                schema: new Schema(
                    type: 'string',
                    enum: [
                        ExportEnum::EXCEL,
                        ExportEnum::CSV,
                    ]
                )
            ),

        ],

        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Download file',
                content: [
                    'application/octet-stream' => new MediaType(
                        mediaType: 'application/octet-stream',
                        schema: new Schema(
                            type: 'string',
                            format: 'binary'
                        )
                    ),
                ],
            ),
        ]
    )]
    #[Route('passphrase/{id}/export', name: 'tasks_export', methods: 'GET', format: 'json')]
    public function export(
        #[MapEntity]
        Passphrase $passphrase,
        #[MapQueryString] Export $exportDTO
    ): HttpResponse {
        try {
            return $this->exportFactory->createExport($exportDTO->type, $passphrase);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse($exception->getMessage(), HttpResponse::HTTP_NOT_FOUND);
        }
    }
}
