<?php

/**
 * PrintNode_Printer
 *
 * Object representing a Printer in PrintNode API
 *
 * @property-read int $id
 * @property PrintNode_Computer $computer
 * @property string $name
 * @property string $description
 * @property object $capabilities
 * @property boolean $default
 * @property DateTime $createTimestamp
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