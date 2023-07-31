<?php

namespace App\Tests\Service;

use App\Entity\Request;
use App\Enum\RequestStatus;
use App\Exception\RequestNotFoundException;
use App\Model\CreateRequest;
use App\Model\CreateRequestResponse;
use App\Model\ListRequest;
use App\Model\RequestListItem;
use App\Model\RequestListResponse;
use App\Model\UpdateRequest;
use App\Model\UpdateRequestResponse;
use App\Repository\RequestRepository;
use App\Service\RequestService;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionObject;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RequestServiceTest extends TestCase
{
    public function testGetRequestList(): void
    {
        $repository = $this->createMock(RequestRepository::class);
        $repository
            ->expects($this->once())
            ->method('matching')
            ->willReturn(new ArrayCollection([$this->createEntity()]));

        $em = $this->createStub(EntityManagerInterface::class);

        $bus = $this->createStub(MessageBusInterface::class);

        $rateLimiter = $this->createStub(RateLimiterFactory::class);

        $service = new RequestService($em, $repository, $bus, $rateLimiter);

        $item = new RequestListItem();
        $item
            ->setId(7)
            ->setStatus('active')
            ->setName('Test')
            ->setEmail('test@test.com')
            ->setComment('Test comment')
            ->setMessage('Test message')
            ->setCreatedAt(1690675200)
            ->setUpdatedAt(1690848000);
        $expected = (new RequestListResponse())->setItems([$item]);

        $this->assertEquals($expected, $service->getRequestList(new ListRequest()));
    }

    public function testUpdateRequest(): void
    {
        $repository = $this->createMock(RequestRepository::class);
        $repository
            ->expects($this->once())
            ->method('findById')
            ->with(7)
            ->willReturn($this->createEntity());

        $em = $this->createStub(EntityManagerInterface::class);

        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())->method('dispatch');

        $rateLimiter = $this->createStub(RateLimiterFactory::class);

        $service = new RequestService($em, $repository, $bus, $rateLimiter);

        $expected = new UpdateRequestResponse();
        $expected->setId(7);

        $this->assertEquals($expected, $service->updateRequest(7, new UpdateRequest()));
    }

    private function createEntity(): Request
    {
        $request = new Request();
        $this->setEntityId($request, 7);
        $request
            ->setStatus(RequestStatus::Active)
            ->setName('Test')
            ->setEmail('test@test.com')
            ->setComment('Test comment')
            ->setMessage('Test message')
            ->setCreatedAt(new DateTimeImmutable('2023-07-30'))
            ->setUpdatedAt(new DateTimeImmutable('2023-08-01'));
        return $request;
    }

    private function setEntityId(Request $request, int $value): void
    {
        $class = new ReflectionClass($request);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($request, $value);
        $property->setAccessible(false);
    }
}
