<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Enum\RequestStatus;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $request = new Request();
        $request
            ->setName('Test User')
            ->setEmail('test@test.com')
            ->setMessage('Need some work')
            ->setStatus(RequestStatus::Resolved)
            ->setComment('123');
        $manager->persist($request);

        $request = new Request();
        $request
            ->setName('Another test user')
            ->setEmail('another_user@test.com')
            ->setMessage('Another request')
            ->setStatus(RequestStatus::Active);
        $manager->persist($request);

        $manager->flush();
    }
}
