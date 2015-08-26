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
    
    protected $Account;
    
    public $ApiKeys;
    
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
    
    public function addAccount(\PrintNode\Entity\Account $Account)
    {
        
        $this->Account = $Account;
        
    }
    
}
