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
    
    protected $id;
    protected $name;
    protected $inet;
    protected $inet6;
    protected $hostname;
    protected $version;
    protected $jre;
    protected $systemInfo;
    protected $acceptOfflinePrintJobs;
    protected $createTimestamp;
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
    
    public function viewPrinters($printerSet = null)
    {
        
        
        
    }
    
    public function viewPrintJobs()
    {
        
        
        
    }
    
    public function viewScales()
    {
        
        
        
    }
    
}
