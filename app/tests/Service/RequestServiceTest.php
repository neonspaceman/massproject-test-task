<?php

namespace App\Tests\Service;

use App\Entity\Request;
use App\Enum\RequestStatus;
use App\Model\RequestListItem;
use App\Model\RequestListResponse;
use App\Repository\RequestRepository;
use App\Service\RequestService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionObject;

class RequestServiceTest extends TestCase
{
    public function testGetRequestList(): void
    {
        $repository = $this->createMock(RequestRepository::class);
        $repository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$this->createEntity()]);

        $em = $this->createStub(EntityManagerInterface::class);

        $service = new RequestService($em, $repository);

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

        $this->assertEquals($expected, $service->getRequestList());
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
