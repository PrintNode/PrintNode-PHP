<?php

namespace PrintNode;

abstract class Entity implements \JsonSerializable
{
    
    /**
     * Reference to the client
     * @var Client
     */
    protected $client;
    
    /**
     * Class constructor
     * @param \PrintNode\Client $parentClient
     */
    public function __construct(Client $parentClient) {
        
        $this->client = $parentClient;
        
    }
    
    /**
     * Set property on entity
     * @param mixed $propertyName
     * @param mixed $value
     * @return void
     */
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
     * Get property on entity
     * @param mixed $propertyName
     * @return mixed
     */
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

    /**
     * Uses the responseMap property to map values in JSON passed in the 
     * argument to the properties.
     * 
     * @param type $json
     * @return boolean
     */
    public function mapValuesFromJson($json)
    {
        
        foreach (static::$responseMap as $responseKey => $responseMapType) {
            
            if (isset($json->$responseKey)) {
                
                if ($responseMapType === null) {
                    $this->$responseKey = $json->$responseKey;
                } else {
                    
                    $childItem = $this->client->mapJsonToEntity($json->$responseKey, $responseMapType);
                    
                    $this->$responseKey = $childItem;
                            
                }
                
            }
            
        }
        
        return true;
        
    }
    
    /**
     * Implements the jsonSerialize method
     * 
     * @return string
     */
    public function jsonSerialize()
    {
        
        $json = array();
        
        foreach (static::$responseMap as $responseKey => $responseMapType) {
            
            if ($this->$responseKey === null) {
                continue;
            }
            
            $json[$responseKey] = $this->$responseKey;
            
        }
        
        return $json;
        
    }
    
    /**
     * Implements the toString magic method.
     * 
     * @return string
     */
    public function __toString() {
        
        return print_r(\json_decode(\json_encode($this->jsonSerialize()), true), true);
        
    }
    
}