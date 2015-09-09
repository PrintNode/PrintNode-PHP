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
    
    /**
     * Printer Id
     * @var int
     */
    protected $id;
    
    /**
     * The computer object that this printer is attached to.
     * @var \PrintNode\Entity\Computer
     */
    protected $computer;
    
    /**
     * The name of the printer
     * @var string
     */
    protected $name;
    
    /**
     * The description of the printer reported by the client
     * @var string
     */
    protected $description;
    
    /**
     * The capabilities of the printer reported by the client
     * @var \PrintNode\Entity\PrinterCapabilities
     */
    protected $capabilities;
    
    /**
     * Flag that indicates if this is the default printer for this computer
     * @var bool
     */
    protected $default;
    
    /**
     * The timestamp of the response
     * @var string
     */
    protected $createTimestamp;
    
    /**
     * The state of the printer reported by the client
     * @var string
     */
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
    
}
