<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * Whoami
 *
 * Object representing a Whoami request in PrintNode API
 *
 * @property-read int $id
 * @property-read string $firstname
 * @property-read string $lastname
 * @property-read string $email
 * @property-read bool $canCreateSubAccounts
 * @property-read string $creatorEmail
 * @property-read string $creatorRef
 * @property-read int[] $childAccounts
 * @property-read string $credits
 * @property-read int $numComputers
 * @property-read int $totalprints
 * @property-read string[] $versions
 * @property-read int[] $connected
 * @property-read string[] $Tags
 * @property-read string[] $ApiKeys
 * @property-read string $state
 * @property-read string[] $permissions
 */
class Whoami extends Entity
{
    
    /**
     * Id
     * @var string 
     */
    protected $id;
    
    /**
     * First Name
     * @var string
     */
    protected $firstname;
    
    /**
     * Last Name
     * @var string
     */
    protected $lastname;
    
    /**
     * Email Address
     * @var string
     */
    protected $email;
    
    /**
     * Can create sub account flag
     * @var bool
     */
    protected $canCreateSubAccounts;
    
    /**
     * The Email account of the account that created this account
     * @var string
     */
    protected $creatorEmail;
    
    /**
     * The creation reference set when the accout 
     * @var string
     */
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
    
    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'id' => null,
        'firstname' => null,
        'lastname' => null,      
        'email' => null,
        'canCreateSubAccounts' => null,
        'creatorEmail' => null,
        'creatorRef' => null,
        'childAccounts' => null,
        'credits' => null,
        'numComputers' => null,
        'totalPrints' => null,
        'versions' => null,
        'connected' => null,
        'Tags' => null,
        'ApiKeys' => null,
        'state' => null,
        'permissions' => null, 
    );
    
}
