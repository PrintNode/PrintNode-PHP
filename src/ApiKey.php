<?php

namespace PrintNode;

class ApiKey implements Credentials
{

	private $apikey;
	private $apiurl = "https://apidev.printnode.com/";

	public function __construct($apikey)
	{
		$this->apikey=$apikey;
	}

	public function __toString()
	{
		return $this->apikey . ':';
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
