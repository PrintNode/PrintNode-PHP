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
     * The creation reference set when the account was created
     * @var string
     */
    protected $creatorRef;
    
    /**
     * Any child accounts present on this account
     * @var array
     */
    protected $childAccounts;
    
    /**
     * The number of print credits remaining on this account
     * @var string
     */
    protected $credits;
    
    /**
     * The number of computers active on this account
     * @var string
     */
    protected $numComputers;
    
    /**
     * Total number of prints made on this account
     * @var int
     */
    protected $totalPrints;
    
    /**
     * Array of versions set on this account
     * @var array
     */
    protected $versions;
    
    /**
     * Array of computer Ids signed in on this account
     * @var array
     */
    protected $connected;
    
    /**
     * Array of tags set on this account
     * @var array
     */
    protected $Tags;
    
    /**
     * Array of all the api keys set on this account
     * @var array 
     */
    protected $ApiKeys;
    
    /**
     * 
     * @var array 
     */
    protected $state;
    
    /**
     * Array of the permissions set on this account
     * @var array 
     */
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
