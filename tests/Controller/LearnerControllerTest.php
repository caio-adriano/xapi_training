<?php

namespace App\Tests\Controller;

use App\Entity\Learner;
use App\Lib\Test\AbstractApiTestCase as ApiTestCase;
use App\Tests\DataProvider\TraitLearnerDataProvider;

class LearnerControllerTest extends ApiTestCase
{
    use TraitLearnerDataProvider;

    /**
     * @dataProvider wrongLearnerData
     */
    public function testCreateLeanerWithWrongData(array $data)
    {
        $learner = new Learner();
        $learner->load($data);

        $contentJson = $this->serializer->serialize($learner, 'json');

        $this->client->request(
            'POST',
            '/v2.0/learners',
            [],
            [],
            [],
            $contentJson
        );
        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($responseContent);
        $this->assertStringContainsString('"message":', $responseContent);
    }

    public function testCreateLearnerWithDefaultAttributesValueEqualNull(): Learner
    {
        $learner = new Learner([
            'login' => 'test01',
            'email' => 'test01@test.com',
            'language' => null,
            'entityID' => null,
            'enabled' => null,
        ]);

        $contentJson = $this->serializer->serialize($learner, 'json');

        $this->client->request(
            'POST',
            '/v2.0/learners',
            [],
            [],
            [],
            $contentJson
        );

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($responseContent);

        return $this->serializer->deserialize($responseContent, Learner::class, 'json');
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
    }
}
