<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * 
 * @property-read string $Account
 * @property-read string $ApiKeys
 * @property-read string $Tags
 */

class ChildAccount extends Entity
{
    
    /**
     * A printnode Account object
     * @var \PrintNode\Entity\Account
     */
    public $Account;
    
    /**
     * An array of API keys
     * @var array
     */
    public $ApiKeys;
    
    /**
     * An array of tags
     * @var array
     */
    public $Tags;
    
    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'Account' => '\PrintNode\Entity\Account',
        'ApiKeys' => null,
        'Tags' => null,
    );
    
}
