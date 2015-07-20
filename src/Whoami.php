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
class Whoami extends Entity
{
	protected $id;
	protected $firstname;
	protected $lastname;
	protected $email;
	protected $canCreateSubAccounts;
	protected $creatorEmail;
	protected $creatorRef;
	protected $childAccounts;
	protected $credits;
	protected $numComputers;
	protected $totalPrints;
	protected $versions;
	protected $connected;
	protected $Tags;
	protected $ApiKeys;
	protected $state;
	protected $permissions;

    public function foreignKeyEntityMap()
    {
        return array();
    }
}