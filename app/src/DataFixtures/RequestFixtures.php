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
            ->setStatus(RequestStatus::Active)
            ->setComment('123')
        ;
        $manager->persist($request);
        $manager->flush();
    }
}
