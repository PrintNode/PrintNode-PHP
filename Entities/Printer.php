<?php

namespace PrintNode\Entities;

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
 * @property-read \DateTime $createTimestamp
 * @property-read string $state
 */
class Printer extends BaseEntity
{
    protected $id;
    protected $computer;
    protected $name;
    protected $description;
    protected $capabilities;
    protected $default;
    protected $createTimestamp;
    protected $state;

    public function foreignKeyEntityMap()
    {
        return array(
            'computer' => 'PrintNode\Entities\Computer'
        );
    }
}