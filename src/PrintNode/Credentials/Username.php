<?php

namespace PrintNode\Credentials;

class Username extends \PrintNode\Credentials
{

    public function __construct ($username, $password, array $childAccountOptions = array(), array $headers = array())
    {
        $this->setBasicAuthHeader($username, $password);
        $this->setHeader('X-Auth-With-Account-Credentials', 'true');
        $this->parseChildAccountOptions($childAccountOptions);
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
    }

}
