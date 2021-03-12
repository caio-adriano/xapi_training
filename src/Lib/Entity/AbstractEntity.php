<?php

namespace App\Lib\Entity;

abstract class AbstractEntity
{
    public function __construct(array $data = [])
    {
        if ($data) {
            $this->load($data);
        }
    }

    /**
     * @param array $data Entity attributes
     */
    public function load(array $data): void
    {
        foreach ($data as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{"set{$attribute}"}($value);
            } else {
                $message = "The attribute {$attribute} not exist";
                throw new \FOS\RestBundle\Exception\InvalidParameterException($message);
            }
        }

        return;
    }
}
