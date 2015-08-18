<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * Account
 *
 * Object representing an Account to POST in PrintNode API
 *
 * @property-read string[string] $Account
 * @property-read string[] $ApiKeys
 * @property-read string[string] $Tags
 */
class AccountDetail extends Entity
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
    protected $state;

}
