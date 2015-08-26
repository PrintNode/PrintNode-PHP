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
        
        $this->apiKey = $apiKey;
        
    }

}
