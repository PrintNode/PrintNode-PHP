<?php

namespace PrintNode;

class EmailPasswordCredentials implements Credentials
{
	private $email;
	private $password;
	private $child_header = array();
	private $other_headers = array();

	public function setChildAccountById($id)
	{
		$this->child_header = array("X-Child-Account-By-Id: ".$id);
	}

	public function setChildAccountByEmail($email)
	{
		$this->child_header = array("X-Child-Account-By-Email: ".$email);
	}

	public function setChildAccountByCreatorRef($creatorRef)
	{
		$this->child_header = array("X-Child-Account-By-CreatorRef: ".$creatorRef);
	}

	public function getHeaders()
	{
		return array_merge($this->child_header,$this->other_headers);
	}

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
