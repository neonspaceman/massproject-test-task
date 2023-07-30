<?php

namespace App\Service;

use App\Entity\Request;
use App\Model\CreateRequest;
use App\Model\RequestListItem;
use App\Model\RequestListResponse;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;

class RequestService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RequestRepository $requestRepository
    )
    {
    }

    public function getRequestList(): RequestListResponse
    {
        $requests = $this->requestRepository->findAll();
        $items = array_map(function(Request $request): RequestListItem {
            return (new RequestListItem())
                ->setId($request->getId())
                ->setName($request->getName())
                ->setEmail($request->getEmail())
                ->setMessage($request->getMessage())
                ->setStatus($request->getStatus()->value)
                ->setComment($request->getComment() ?? '')
                ->setCreatedAt($request->getCreatedAt()->getTimestamp())
                ->setUpdatedAt($request->getUpdatedAt()->getTimestamp());
        }, $requests);
        return (new RequestListResponse())
            ->setItems($items);
    }

    public function createRequest(CreateRequest $request): void
    {

    }
}
