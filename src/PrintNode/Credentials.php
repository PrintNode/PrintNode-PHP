<?php

namespace PrintNode;

class Credentials implements CredentialsInterface
{
    private $apikey;
    private $emailPassword;

    public function __construct()
    {
    }

    /**
     * return correct authentcation method 
     * @param void
     * @return string
     * */
    public function __toString()
    {
        $string = '';
        if (isset($this->apikey)) {
            $string = $this->apikey;
        } else {
            $string = $this->emailPassword;
        }
        return $string;
    }

    /**
     * Set email and password for Email:Password authentication.
     * @param string $email
     * @param string $password
     * */

    public function setEmailPassword($email, $password)
    {
        if (!isset($this->apikey)) {
            $this->emailpassword = $email.': '.$password;
        } else {
            throw new \Exception(
                print("ApiKey already set.")
            );
        }
    }

    /**
     * Set email and password for Email:Password authentication.
     * @param string $apikey
     * */
    public function setApiKey($apikey)
    {
        if (!isset($this->emailPassword)) {
            $this->apikey = $apikey . ':';
        } else {
            throw new \Exception(
                print("EmailPassword already set.")
            );
        }
    }

    /**
     * Set property on object
     * @param mixed $propertyName
     * @param mixed $value
     * @return void
     * */
    public function __set($propertyName, $value)
    {
        if (!property_exists($this, $propertyName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s does not have a property named %s',
                    get_class($this),
                    $propertyName
                )
            );
        }

        if ($propertyName == "headers") {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s if you want to update headers, use $credentials->account_by(type,data)',
                    get_class($this)
                )
            );
        }

        $this->$propertyName = $value;
    }

    /**
     * Get property on object
     * @param mixed $propertyName
     * @return mixed
     * */
    public function __get($propertyName)
    {
        if (!property_exists($this, $propertyName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s does not have a property named %s',
                    get_class($this),
                    $propertyName
                )
            );
        }

        return $this->$propertyName;
    }
}
