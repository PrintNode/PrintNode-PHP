<?php

namespace PrintNode;

class ApiKey implements Credentials
{

	private $apikey;
	private $apiurl = "https://api.printnode.com/";
	private $headers = [];
	private $header_types = array(
		'id' => 'X-Child-Account-By-Id',
		'email' => 'X-Child-Account-By-Email',
		'creatorRef' => 'X-Child-Account-By-CreatorRef'
	);


	public function __construct()
	{
		$args_array = func_get_args();
		if (count($args_array) == 0 || count($args_array) > 3){
			throw new \InvalidArgumentException(
				sprintf(
					'incorrect number of arguments- either ApiKey($apikey) or ApiKey($apikey,$type_of_child_lookup,$child_(id or email or creatorref)'
				)
			);
		}
		foreach($args_array as $donk){
			echo($donk);
		}
		$this->apikey=$args_array[0];
		if (count($args_array) == 3){
			$this->headers = [$this->header_types[$args_array[1]]. ': ' .$args_array[2]];
		}
		var_dump($this->headers);
	}

	public function account_by($type, $data)
	{
		$this->headers = [$this->header_types[$type]. ':' .$data];
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
