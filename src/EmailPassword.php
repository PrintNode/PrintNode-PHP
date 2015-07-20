<?php

namespace PrintNode;

class EmailPassword implements Credentials
{

	private $email;
	private $password;
	private $apiurl = "https://api.printnode.com/";
	private $headers = ['X-Auth-With-Account-Credentials: API'];

	public function __construct($email,$password)
	{
		$this->email=$email;
		$this->password=$password;
	}

	public function __toString()
	{
		return $this->email . ':' . $this->password;
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

		if ($propertyName == "headers"){
			throw new \InvalidArgumentException(
				sprintf(
					'%s cannot have headers changed.',
					get_class($this)
				)
			);
		}

        $this->$propertyName = $value;
    }

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
?>
