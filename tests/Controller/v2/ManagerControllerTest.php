<?php

namespace App\Tests\Controller\v2;

use App\Entity\Manager;
use App\Lib\Test\AbstractApiTestCase;

class ManagerControllerTest extends AbstractApiTestCase
{

    public function testCreateManager(): string
    {
        $manager = new Manager([
            'name' => 'Ozzy',
        ]);

        $this->client->request(
            'POST',
            '/v2/managers',
            [],
            [],
            [],
            $this->serializer->serialize($manager, 'json')
        );

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);

        return $responseContent;
    }

    /**
     * @depends testCreateManager
     */
    public function testIndexManager(string $manager): void
    {
        $this->client->request('GET', '/v2/managers');

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);
        $this->assertStringContainsString($manager, $responseContent);

        return;
    }

    /**
     * @depends testCreateManager
     */
    public function testShowManager(string $manager): void
    {
        $managerEntity = $this->serializer->deserialize($manager, Manager::class, 'json');
        $this->client->request('GET', "/v2/managers/{$managerEntity->getId()}");

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);
        $this->assertStringContainsString($manager, $responseContent);

        return;
    }
}
