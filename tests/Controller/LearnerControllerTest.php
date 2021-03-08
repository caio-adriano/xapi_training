<?php

namespace App\Tests\Controller;

use App\Entity\Learner;
use App\Lib\Test\AbstractApiTestCase as ApiTestCase;

class LearnerControllerTest extends ApiTestCase
{
    public function wrongLearnerData(): array
    {
        return [
            [
                [
                    'login' => '',
                ]
            ],
            [
                [
                    'login' => null,
                ]
            ],
            [
                [
                    'login' => str_pad('test', 110, '_', STR_PAD_BOTH),
                ]
            ],
            [
                [
                    'login' => 'test',
                    'email' => '',
                ]
            ],
            [
                [
                    'login' => 'test',
                    'email' => null,
                ]
            ],
            [
                [
                    'login' => 'test', // valid
                    'email' => 'test.com', // invalid
                ]
            ],
            [
                [
                    'login'    => 'test', // valid
                    'email'    => 'teste@test.com', // valid
                    'language' => 'tst', // invalid
                ]
            ],
            [
                [
                    'login'    => 'test',          // valid
                    'email'    => 'test@test.com', // valid
                    'language' => 'pt',            // valid
                    'timezone' => 'america'        // invalid
                ]
            ],
            [
                [
                    'login'    => 'test',              // valid
                    'email'    => 'test@test.com',     // valid
                    'language' => 'pt',                // valid
                    'timezone' => 'America/Sao_Paulo', // valid
                    'entityID' => 0,                   // invalid
                ]
            ],
            [
                [
                    'login'    => 'test',              // valid
                    'email'    => 'test@test.com',     // valid
                    'language' => 'pt',                // valid
                    'timezone' => 'America/Sao_Paulo', // valid
                    'entityID' => -5,                  // invalid
                ]
            ],
            [
                [
                    'login'     => 'test',              // valid
                    'email'     => 'test@test.com',     // valid
                    'language'  => 'pt',                // valid
                    'timezone'  => 'America/Sao_Paulo', // valid
                    'entityID'  => 10,                  // valid
                    'managerID' => -5                   // invalid
                ]
            ],
            [
                [
                    'login'         => 'test',              // valid
                    'email'         => 'test@test.com',     // valid
                    'language'      => 'pt',                // valid
                    'timezone'      => 'America/Sao_Paulo', // valid
                    'entityID'      => 10,                  // valid
                    'managerID'     => 15,                  // valid
                    'enabledFrom'   => '19-10-2020',        // invalid (BR date)
                ]
            ],
            [
                [
                    'login'         => 'test',              // valid
                    'email'         => 'test@test.com',     // valid
                    'language'      => 'pt',                // valid
                    'timezone'      => 'America/Sao_Paulo', // valid
                    'entityID'      => 10,                  // valid
                    'managerID'     => 15,                  // valid
                    'enabledFrom'   => '2020-10-12',        // valid
                    'enabledUntil'  => '25-02-2021',        // invalid
                ]
            ],
            [
                [
                    'login'         => 'test',              // valid
                    'email'         => 'test@test.com',     // valid
                    'language'      => 'pt',                // valid
                    'timezone'      => 'America/Sao_Paulo', // valid
                    'entityID'      => 10,                  // valid
                    'managerID'     => 15,                  // valid
                    'enabledFrom'   => '2020-10-12',        // valid
                    'enabledUntil'  => '2020-05-10',        // invalid
                ]
            ],
        ];
    }

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
            '/learners',
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
            '/learners',
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