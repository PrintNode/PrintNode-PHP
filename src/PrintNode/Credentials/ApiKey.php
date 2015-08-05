<?php

namespace PrintNode\Credentials;

class ApiKey extends \PrintNode\Credentials
{

    public function __construct ($apiKey, array $childAccountOptions = array(), array $headers = array())
    {
        $this->setBasicAuthHeader($apiKey, '');
        $this->parseChildAccountOptions($childAccountOptions);
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
    }

}
