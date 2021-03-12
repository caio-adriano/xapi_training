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
}
