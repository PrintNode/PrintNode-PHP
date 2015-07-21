<?php

namespace PrintNode;

/**
 * State
 *
 * Object representing a State in PrintNode API
 *
 * @property-read int $printJobId
 * @property-read string $state
 * @property-read string $message
 * @property-read object $data
 * @property-read string $clientVersion
 * @property-read DateTime $createTimestamp
 * @property-read int $age
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
