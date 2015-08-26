<?php

namespace PrintNode;

abstract class EntityDynamic extends Entity
{
    
    /**
     * Reference to the client
     * @var Client
     */
    protected static $protectedProperties = array(
        'client',
    );
    
    /**
     * Set property on entity
     * @param mixed $propertyName
     * @param mixed $value
     * @return void
     */
    public function __set($propertyName, $value)
    {
        
        if (\in_array($propertyName, self::$protectedProperties)) {
            throw new \PrintNode\Exception\InvalidArgumentException($propertyName . ' is a protected property.');
        }
        
        $this->$propertyName = $value;
        
    }
    
    /**
     * Maps a json object to this entity
     * 
     * @param string $json The JSON to map to this entity
     * @return string
     */
    public function mapValuesFromJson($json)
    {
        
        foreach ($json as $key => $value) {
            $this->$key = $value;
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
        
        $refClass = new \ReflectionClass($this);
        
        $properties = get_object_vars($this);
        
        $json = array();
        
        foreach ($properties as $property => $value) {
            
            if (in_array($property, self::$protectedProperties)) {
                continue;
            }
            
            $json[$property] = $value;
            
        }
        
        return $json;
        
    }
    
}