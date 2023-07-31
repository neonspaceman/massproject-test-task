<?php

namespace App\Controller;


use App\Attribute\QueryString;
use App\Attribute\RequestBody;
use App\Enum\RequestStatus;
use App\Model\CreateRequest;
use App\Model\ListRequest;
use App\Model\UpdateRequest;
use App\Model\CreateRequestResponse;
use App\Model\ErrorResponse;
use App\Model\RequestListResponse;
use App\Model\UpdateRequestResponse;
use App\Service\RequestService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RequestsController extends AbstractController
{
    public function __construct(
        private RequestService $requestService
    )
    {
    }

    #[Route(path: '/api/v1/requests', methods: 'GET')]
    #[OA\QueryParameter(name: 'status', description: 'Filter by request status', required: false, content: new Model(type: RequestStatus::class))]
    #[OA\QueryParameter(name: 'from', description: 'Filter by request created at from (timestamp)', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\QueryParameter(name: 'to', description: 'Filter by request created at from (timestamp)', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Tag(name: 'Requests')]
    #[OA\Response(response: 200, description: "Get requests list", content: new Model(type: RequestListResponse::class))]
    public function list(#[QueryString] ListRequest $query): Response
    {
        return $this->json($this->requestService->getRequestList($query));
    }

    #[Route(path: '/api/v1/requests/{id}', methods: 'PUT')]
    #[IsGranted('ROLE_USER')]
    #[OA\Tag(name: 'Requests')]
    #[OA\HeaderParameter(name: 'X-AUTH-USER', description: 'User\'s email', required: true)]
    #[OA\HeaderParameter(name: 'X-AUTH-PASSWORD', description: 'User\'s password', required: true)]
    #[OA\RequestBody(content: new Model(type: UpdateRequest::class))]
    #[OA\Parameter(name: 'id', description: 'Request Id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Update request', content: new Model(type: UpdateRequestResponse::class))]
    #[OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: 404, description: 'Request not found', content: new Model(type: ErrorResponse::class))]
    public function update(int $id, #[RequestBody] UpdateRequest $updateRequest): Response
    {
        return $this->json($this->requestService->updateRequest($id, $updateRequest));
    }

    #[Route(path: '/api/v1/requests', methods: 'POST')]
    #[OA\Tag(name: 'Requests')]
    #[OA\RequestBody(content: new Model(type: CreateRequest::class))]
    #[OA\Response(response: 200, description: 'Create request', content: new Model(type: CreateRequestResponse::class))]
    #[OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: 429, description: 'Too many request', content: new Model(type: ErrorResponse::class))]
    public function create(#[RequestBody] CreateRequest $request): Response
    {
        return $this->json($this->requestService->createRequest($request));
    }
}
