<?php

namespace App\Lib\Test;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractApiTestCase extends WebTestCase
{
    /** @var KernelBrowser */
    protected $client;

    /** @var Serializer */
    protected $serializer;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->serializer = new Serializer([
            new ObjectNormalizer(),
            new ArrayDenormalizer(),
        ], [
            new JsonEncoder(),
        ]);
    }
}