<?php

namespace AppBundle\Checker;

class Availability
{
    protected $reference;
    protected $zone;

    public function __construct($reference, $zone)
    {
        $this->reference = $reference;
        $this->zone = $zone;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getZone()
    {
        return $this->zone;
    }

    public function __toString()
    {
        return sprintf(
            'The server "%s" is available in zone "%s".',
            $this->reference,
            $this->zone
        );
    }
}
