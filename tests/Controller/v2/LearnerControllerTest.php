<?php

namespace App\Tests\Controller\v2;

use App\Entity\Learner;
use App\Lib\Test\AbstractApiTestCase as ApiTestCase;

class LearnerControllerTest extends ApiTestCase
{

    public function testCreateLearnerWithDefaultAttributesValueEqualNull(): Learner
    {
        $learner = new Learner([
            'login'    => 'test02',
            'email'    => 'test02@test.com',
            'language' => null,
            'entityID' => null,
            'enabled'  => null,
        ]);

        $contentJson = $this->serializer->serialize($learner, 'json');

        $this->client->request(
            'POST',
            '/v2/learners',
            [],
            [],
            [],
            $contentJson
        );

        $reponseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($reponseContent);

        return $this->serializer->deserialize($reponseContent, Learner::class, 'json');
    }

    /**
     * @depends testCreateLearnerWithDefaultAttributesValueEqualNull
     */
    public function testLearnerAttributesWithDefaultValue(Learner $learner): void
    {
        $this->assertNotNull($learner->getEntityID());
        $this->assertIsNumeric($learner->getEntityID());
        $this->assertEquals(1, $learner->getEntityID());

        $this->assertNotNull($learner->getEnabled());
        $this->assertIsBool($learner->getEnabled());
        $this->assertTrue($learner->getEnabled());

        $this->assertNotNull($learner->getLanguage());
        $this->assertIsString($learner->getLanguage());
        $this->assertEquals('en', $learner->getLanguage());


        return;
    }

    /**
     * @depends testCreateLearnerWithDefaultAttributesValueEqualNull
     */
    public function testUpdate(Learner $learner): void
    {
        $data = [
            'firstName' => 'test',
            'entityID'  => 10,
            'email' => 'test@test02.com.br',
        ];

        $this->client->request(
            'PATCH',
            "/v2/learners/{$learner->getId()}",
            [],
            [],
            [],
            json_encode($data)
        );

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);

        $learnerUpdated = $this->serializer->deserialize($responseContent, Learner::class, 'json');

        $this->assertIsString($learnerUpdated->getFirstName());
        $this->assertEquals('test', $learnerUpdated->getFirstName());

        $this->assertIsInt($learnerUpdated->getEntityID());
        $this->assertEquals(10, $learnerUpdated->getEntityID());

        $this->assertIsString($learnerUpdated->getEmail());
        $this->assertEquals('test@test02.com.br', $learnerUpdated->getEmail());

        return;
    }
}
