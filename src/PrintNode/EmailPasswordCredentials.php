<?php

namespace PrintNode;

class EmailPasswordCredentials implements Credentials
{
	private $email;
	private $password;

    public function __construct($email,$password)
	{
		$this->email = $email;
		$this->password = $password;
    }

    /**
     * return correct authentcation method 
     * @param void
     * @return string
     * */
    public function __toString()
	{
		return $this->email . ': ' . $password;
    }

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
