<?php

namespace PrintNode\Credentials;

class ApiKey extends \PrintNode\Credentials
{

    /**
     * Class constuctor
     * @param string $apiKey API Key to be used for authentication
     */
    public function __construct ($apiKey)
    {
        
        if (!\is_string($apiKey)
            || (mb_strlen(trim($apiKey)) == 0)) {
            
            $emsg = 'Argument 1 passed to PrintNode\Credentials\ApiKey::__construct() must be a valid ApiKey string';
            
            throw new \PrintNode\Exception\InvalidArgumentException($emsg);
            
        }
        
        $this->apiKey = $apiKey;
        
    }

}
