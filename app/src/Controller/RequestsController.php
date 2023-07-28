<?php

namespace App\Controller;


use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;

class RequestsController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route(path: '/api/v1/requests', methods: 'GET')]
    #[OA\Response(response: 200, description: "Some description")]
    public function action(): Response
    {
        return $this->json('not implemented');
    }
}