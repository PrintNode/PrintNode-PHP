<?php

namespace PrintNode;

/**
 * Printer
 *
 * Object representing a Printer in PrintNode API
 *
 * @property-read int $id
 * @property-read Computer $computer
 * @property-read string $name
 * @property-read string $description
 * @property-read object $capabilities
 * @property-read boolean $default
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class State extends Entity
{
    protected $printJobId;
    protected $state;
    protected $message;
    protected $data;
    protected $clientVersion;
    protected $createTimestamp;
    protected $age;

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}