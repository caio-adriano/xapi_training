<?php

namespace App\Tests\Controller;

use App\Entity\Manager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ManagerControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    /** @var Serializer */
    private $serializer;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->serializer = new Serializer([
            new ObjectNormalizer(),
            new ArrayDenormalizer(),
        ], [
            new JsonEncoder()
        ]);
    }

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


}