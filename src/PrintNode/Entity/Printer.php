<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

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
class Printer extends Entity
{
    
    protected $id;
    protected $computer;
    protected $name;
    protected $description;
    protected $capabilities;
    protected $default;
    protected $createTimestamp;
    protected $state;

    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'id' => null,
        'computer' => '\PrintNode\Entity\Computer',
        'name' => null,
        'description' => null,
        'capabilities' => '\PrintNode\Entity\PrinterCapabilities',
        'default' => null,
        'createTimestamp' => null,
        'state' => null,
    );
    
    public function viewPrintJobs()
    {
        
        
        
    }
    
}
