<?php

namespace App\Tests\Controller;

use App\Entity\Manager;
use App\Lib\Test\AbstractApiTestCase;

class ManagerControllerTest extends AbstractApiTestCase
{
    /**
     * @depends testCreateManager
     */
    public function testShowManager($manager): void
    {
        $managerEntity = $this->serializer->deserialize($manager, Manager::class, 'json');

        $this->client->request('GET', "/managers/{$managerEntity->getId()}");

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);
        $this->assertStringContainsString($manager, $responseContent);
    }

    /**
     * @depends testCreateManager
     */
    public function testIndexManager($manager): void
    {
        $this->client->request('GET', '/managers');

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);
        $this->assertStringContainsString($manager, $responseContent);
    }

    public function testCreateManager()
    {
        $sendManager = new Manager();
        $sendManager->setName('harry');

        $this->client->request(
            'POST',
            '/managers',
            [],
            [],
            [],
            $this->serializer->serialize($sendManager, 'json'),
        );

        $responseContent = $this->client->getResponse()->getContent();
        $manager = $this->serializer->deserialize($responseContent, Manager::class, 'json');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);
        $this->assertObjectHasAttribute('id', $manager);
        $this->assertNotNull($manager->getId());
        $this->assertIsInt($manager->getId());
        $this->assertEquals($sendManager->getName(), $manager->getName());

        return $responseContent;
    }

    public function testCreateManagerWithInvalidNameLength()
    {
        $manager = new Manager();

        $manager->setName(str_pad('test', 256, '_', STR_PAD_BOTH));

        $this->assertGreaterThan(255, strlen($manager->getName()));
        $this->client->request(
            'POST',
            '/managers',
            [],
            [],
            [],
            $this->serializer->serialize($manager, 'json')
        );

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($responseContent);
        $this->assertStringContainsString('"message":', $responseContent);
    }

    public function testCreateManagerWithBlankName()
    {
        $manager = new Manager();

        $manager->setName('');

        $this->client->request(
            'POST',
            '/managers',
            [],
            [],
            [],
            $this->serializer->serialize($manager, 'json'),
        );

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($responseContent);
        $this->assertStringContainsString('"message":', $responseContent);
    }


}