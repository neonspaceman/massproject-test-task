<?php

namespace App\Service;

use App\Entity\Request;
use App\Enum\RequestStatus;
use App\Exception\TooManyRequests;
use App\Message\SendEmailMessage;
use App\Model\CreateRequest;
use App\Model\ListRequest;
use App\Model\UpdateRequest;
use App\Model\CreateRequestResponse;
use App\Model\RequestListItem;
use App\Model\RequestListResponse;
use App\Model\UpdateRequestResponse;
use App\Repository\RequestRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RequestService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestRepository $requestRepository,
        private MessageBusInterface $bus,
        private RateLimiterFactory $anonymousApiLimiter
    )
    {
    }

    private function getCriteriaFromQuery(ListRequest $query): Criteria
    {
        $criteria = new Criteria();
        if ($query->getStatus() !== null){
            $criteria->andWhere(Criteria::expr()->eq('status', $query->getStatus()));
        }
        if ($query->getFrom() !== null){
            $criteria->andWhere(Criteria::expr()->gte('createdAt', (new DateTimeImmutable())->setTimestamp($query->getFrom())));
        }
        if ($query->getTo() !== null){
            $criteria->andWhere(Criteria::expr()->lte('createdAt', (new DateTimeImmutable())->setTimestamp($query->getTo())));
        }
        return $criteria;
    }

    public function getRequestList(ListRequest $query): RequestListResponse
    {
        $requests = $this->requestRepository->matching($this->getCriteriaFromQuery($query));
        $items = array_map(function(Request $request): RequestListItem {
            return (new RequestListItem())
                ->setId($request->getId())
                ->setName($request->getName())
                ->setEmail($request->getEmail())
                ->setMessage($request->getMessage())
                ->setStatus($request->getStatus()->value)
                ->setComment($request->getComment())
                ->setCreatedAt($request->getCreatedAt()->getTimestamp())
                ->setUpdatedAt($request->getUpdatedAt()->getTimestamp());
        }, $requests->toArray());
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
        $this->applyRateLimiter($createRequest->getEmail());

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

    public function applyRateLimiter(string $key): void
    {
        $limiter = $this->anonymousApiLimiter->create($key);
        if ($limiter->consume()->isAccepted() === false){
            throw new TooManyRequests();
        }
    }
}
