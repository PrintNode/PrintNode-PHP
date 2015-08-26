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
    
    protected $mass;
    protected $deviceName;
    protected $deviceNum;
    protected $port;
    protected $count;
    protected $measurement;
    protected $clientReportedCreateTimestamp;
    protected $ntpOffset;
    protected $ageOfData;
    protected $computerId;
    protected $vendor;
    protected $product;
    protected $vendorId;
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
