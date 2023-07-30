<?php

namespace App\Service;

use App\Entity\Request;
use App\Enum\RequestStatus;
use App\Message\SendEmailMessage;
use App\Model\CreateRequest;
use App\Model\UpdateRequest;
use App\Model\CreateRequestResponse;
use App\Model\RequestListItem;
use App\Model\RequestListResponse;
use App\Model\UpdateRequestResponse;
use App\Repository\RequestRepository;
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class RequestService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestRepository $requestRepository,
        private MessageBusInterface $bus
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

    public function updateRequest(int $id, UpdateRequest $updateRequest): UpdateRequestResponse
    {
        $request = $this->requestRepository->findById($id);
        $request
            ->setStatus(RequestStatus::Resolved)
            ->setComment($updateRequest->getComment());
        $this->em->persist($request);
        $this->em->flush();

        $sendEmailMessage = new SendEmailMessage(
            $request->getEmail(),
            'Your request has been answered',
            'Content');
        $this->bus->dispatch($sendEmailMessage);

        return (new UpdateRequestResponse())
            ->setId($request->getId());
    }

    public function createRequest(CreateRequest $createRequest): CreateRequestResponse
    {
        $request = (new Request())
            ->setStatus(RequestStatus::Active)
            ->setEmail($createRequest->getEmail())
            ->setName($createRequest->getName())
            ->setMessage($createRequest->getMessage());
        $this->em->persist($request);
        $this->em->flush();

        return (new CreateRequestResponse())
            ->setId($request->getId());
    }
}
