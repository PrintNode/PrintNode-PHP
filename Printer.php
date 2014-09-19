<?php

/**
 * PrintNode_Printer
 *
 * Object representing a Printer in PrintNode API
 *
 * @property-read int $id
 * @property-read PrintNode_Computer $computer
 * @property-read string $name
 * @property-read string $description
 * @property-read object $capabilities
 * @property-read boolean $default
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class PrintNode_Printer extends PrintNode_Entity
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
            'computer' => 'PrintNode_Computer'
        );
    }
}