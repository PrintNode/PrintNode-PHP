<?php

namespace PrintNode\Credentials;

class Username extends \PrintNode\Credentials
{

    /**
     * Class constuctor
     * @param string $username Username to be used for authentication
     * @param string $password Password to be used for authentication
     */
    public function __construct ($username, $password)
    {
        
        if (!\is_string($username)
            || (mb_strlen(trim($username)) == 0)) {
            
            $emsg = 'Argument 1 passed to PrintNode\Credentials\ApiKey::__construct() must be a valid username string';
            
            throw new \PrintNode\Exception\InvalidArgumentException($emsg);
            
        }
        
        if (!\is_string($password)
            || (mb_strlen(trim($password)) == 0)) {
            
            $emsg = 'Argument 2 passed to PrintNode\Credentials\ApiKey::__construct() must be a valid password string';
            
            throw new \PrintNode\Exception\InvalidArgumentException($emsg);
            
        }
        
        $this->username = $username;
        $this->password = $password;
                
    }

}
