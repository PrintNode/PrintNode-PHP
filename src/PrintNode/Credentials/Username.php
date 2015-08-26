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
        
        $this->username = $username;
        $this->password = $password;
                
    }

}
