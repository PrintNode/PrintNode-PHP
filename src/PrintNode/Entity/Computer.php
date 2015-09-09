<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * PrintNode_Computer
 *
 * Object representing a Computer in PrintNode API
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $inet
 * @property-read string $inet6
 * @property-read string $version
 * @property-read string $jre
 * @property-read object $systemInfo
 * @property-read boolean $acceptOfflinePrintJobs
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class Computer extends Entity
{
    
    /**
     * Computer Id
     * @var int 
     */
    protected $id;
    
    /**
     * Computer Name
     * @var string
     */
    protected $name;
    
    /**
     * Reserved
     * @var mixed
     */
    protected $inet;
        
    /**
     * Reserved
     * @var mixed
     */
    protected $inet6;
    
    /**
     * Reserved
     * @var mixed
     */    
    protected $hostname;
    
    /**
     * Reserved
     * @var mixed
     */
    protected $version;
    
    /**
     * Reserved
     * @var mixed
     */
    protected $jre;
    
    /**
     * Reserved
     * @var mixed
     */
    protected $systemInfo;
    
    /**
     * Reserved
     * @var mixed
     */
    protected $acceptOfflinePrintJobs;
    
    /**
     * The time and date the computer was first registered with PrintNode
     * @var mixed
     */
    protected $createTimestamp;
    
    /**
     * Current state of the computer
     * @var mixed
     */
    protected $state;

    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'id' => null,
        'name' => null,
        'inet' => null,
        'inet6' => null,
        'hostname' => null,
        'version' => null,
        'jre' => null,
        'systemInfo' => null,
        'acceptOfflinePrintJobs' => null,
        'createTimestamp' => null,
        'state' => null,
    );
    
    /**
     * Returns an array of the printers present on this computer
     * 
     * @param int $offset 
     * @param int $limit
     * @param mixed $printerSet
     * @return array
     */
    public function viewPrinters($offset = 0, $limit = 500, $printerSet = null)
    {
        
        return $this->client->viewPrinters($offset, $limit, $printerSet, $this->id);
        
    }
    
    /**
     * 
     * @param type $deviceName
     * @param type $deviceNumber
     */
    public function viewScales($deviceName = null, $deviceNumber = null)
    {
        
        return $this->client->viewScales($this->id, $deviceName, $deviceNumber);
        
    }
    
}
