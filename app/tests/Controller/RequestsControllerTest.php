<?php

namespace App\Tests\Controller;

use App\Controller\RequestsController;
use App\Entity\Request;
use App\Entity\User;
use App\Enum\RequestStatus;
use App\Tests\AbstractControllerTestCase;

class RequestsControllerTest extends AbstractControllerTestCase
{
    public function testList(): void
    {
        $this->createRequestFixtures();

        $this->client->request('GET', '/api/v1/requests');
        $responseContent = json_decode(
            $this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'name', 'email', 'status', 'message', 'createdAt', 'updatedAt'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'email' => ['type' => 'string'],
                            'status' => ['type' => 'string'],
                            'message' => ['type' => 'string'],
                            'createdAt' => ['type' => 'integer'],
                            'updatedAt' => ['type' => 'integer']
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testUpdateSuccess(): void
    {
        $requestId = $this->createRequestFixtures();
        $this->createUserFixtures();

        $this->client->request('PUT', '/api/v1/requests/' . $requestId, [], [], [
            'HTTP_X-AUTH-USER' => 'admin@test.com',
            'HTTP_X-AUTH-PASSWORD' => 'root',
        ], '{"comment": "string"}');
        $responseContent = json_decode(
            $this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id'],
            'properties' => [
                'id' => ['type' => 'integer'],
            ]
        ]);
    }

    public function testUpdateAuthorizationFailed(): void
    {
        $this->createUserFixtures();

        $this->client->request('PUT', '/api/v1/requests/1', [], [], [
            'HTTP_X-AUTH-USER' => 'user@test.com',
            'HTTP_X-AUTH-PASSWORD' => 'wrong_pass',
        ], '{"comment": "string"}');
        $responseContent = json_decode(
            $this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateNotFound(): void
    {
        $this->createUserFixtures();

        $this->client->request('PUT', '/api/v1/requests/1', [], [], [
            'HTTP_X-AUTH-USER' => 'admin@test.com',
            'HTTP_X-AUTH-PASSWORD' => 'root',
        ], '{"comment": "string"}');
        $responseContent = json_decode(
            $this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateSuccess(): void
    {
        $this->client->request(
            'POST', '/api/v1/requests', [], [], [],
            '{"name": "string","email": "test@test.com","message": "string"}');
        $responseContent = json_decode(
            $this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id'],
            'properties' => [
                'id' => ['type' => 'integer'],
            ]
        ]);
    }

    public function testCreateWrongEmail(): void
    {
        $this->client->request(
            'POST', '/api/v1/requests', [], [], [],
            '{"name": "string","email": "string","message": "string"}');
        $responseContent = json_decode(
            $this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400);
    }

    private function createRequestFixtures(): int
    {
        $request = new Request();
        $request
            ->setName('Test User')
            ->setEmail('test@test.com')
            ->setMessage('Need some work')
            ->setStatus(RequestStatus::Resolved)
            ->setComment('123');
        $this->em->persist($request);

        $this->em->flush();

        return $request->getId();
    }

    private function createUserFixtures(): void
    {
        $user = new User();
        $user->setEmail('admin@test.com');
        $user->setPassword('$2y$13$s2kPwV4phkIoZaZ.YIfQZu/NyGiJSzEBKPv8G4N0dmHVkmbNpW7ny'); // root
        $this->em->persist($user);
        $this->em->flush();
    }
}
