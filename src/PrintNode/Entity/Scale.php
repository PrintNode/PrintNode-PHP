<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * Scale
 *
 * Object representing a Scale in PrintNode API
 *
 * @property-read int[] $mass
 * @property-read string $deviceName
 * @property-read int $deviceNum
 * @property-read string $port
 * @property-read int $count
 * @property-read int[string] $measurement
 * @property-read DateTime $clientReportedCreateTimestamp
 * @property-read string $ntpOffset
 * @property-read int $ageOfData
 * @property-read int $computerId
 * @property-read string $product
 * @property-read int $vendorId
 * @property-read int $productId
 */
class Scale extends Entity
{
    
    /**
     * 
     * @var array
     */
    protected $mass;
    
    /**
     * The name of the device
     * @var string
     */
    protected $deviceName;
    
    /**
     * The number of the device on the computer
     * @var int
     */
    protected $deviceNum;
    
    /**
     * The port to which the device is attached
     * @var string 
     */
    protected $port;
    
    /**
     *
     * @var string
     */
    protected $count;
    
    /**
     * The current measurement that the scale is reporting
     * @var array
     */
    protected $measurement;
    
    /**
     * The timestamp of the last message from the client that reported a 
     * measurement
     * @var string
     */
    protected $clientReportedCreateTimestamp;
    
    /**
     *
     * @var mixed 
     */
    protected $ntpOffset;
    
    /**
     * The number of seconds that have elapsed since the last measurement
     * @var int
     */
    protected $ageOfData;
    
    /**
     * The id of the computer to which these scales are attached
     * @var type 
     */
    protected $computerId;
    
    /**
     * The vendor reported by the scale hardware
     * @var string
     */
    protected $vendor;
    
    /**
     * The product name reported by the scale hardware
     * @var string
     */
    protected $product;
    
    /**
     * The vendor id by the scale hardware
     * @var int
     */
    protected $vendorId;
    
    /**
     * The product id reported by the scale hardware
     * @var int
     */
    protected $productId;
    
    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'mass' => null,
        'deviceName' => null,
        'deviceNum' => null,
        'port' => null,
        'count' => null,
        'measurement' => null,
        'clientReportedCreateTimestamp' => null,
        'ntpOffset' => null,
        'ageOfData' => null,
        'computerId' => null,
        'vendor' => null,
        'product' => null,
        'vendorId' => null,
        'productId' => null,
    );
    
}
