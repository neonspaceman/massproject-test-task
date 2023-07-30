<?php

namespace App\Controller;


use App\Attribute\RequestBody;
use App\Model\CreateRequest;
use App\Model\RequestListResponse;
use App\Repository\RequestRepository;
use App\Service\RequestService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RequestService $requestService
    )
    {
    }

    #[Route(path: '/api/v1/requests', methods: 'GET')]
    #[OA\Response(
        response: 200,
        description: "Get requests list",
        content: new Model(type: RequestListResponse::class)
    )]
    public function list(): Response
    {
        return $this->json($this->requestService->getRequestList());
    }

    #[Route(path: '/api/v1/requests/{id}', methods: 'PUT')]
    #[OA\Parameter(name: 'id', description: 'Request Id', in: 'path', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: "Update request")]
    public function update(int $id): Response
    {
        return $this->json('not implemented');
    }

    #[Route(path: '/api/v1/requests', methods: 'POST')]
    #[OA\Response(response: 200, description: "Create request")]
    #[OA\RequestBody(content: new Model(type: CreateRequest::class))]
    public function create(#[RequestBody] CreateRequest $request): Response
    {
        return $this->json('not implemented');
    }
}
