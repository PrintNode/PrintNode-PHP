<?php

namespace PrintNode;

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

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}