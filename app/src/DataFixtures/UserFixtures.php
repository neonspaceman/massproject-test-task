<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Entity\User;
use App\Enum\RequestStatus;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@test.com');
        $user->setPassword($this->hasher->hashPassword($user, 'root'));
        $this->em->persist($user);
        $this->em->flush();
    }
}
