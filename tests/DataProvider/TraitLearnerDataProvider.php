<?php

namespace App\Tests\DataProvider;

trait TraitLearnerDataProvider
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
}