<?php

namespace PrintNode;

abstract class EntityDynamic extends Entity
{
    /**
     * Reference to the client
     * @var Client
     */
    protected static $protectedProperties = [
        'client',
    ];

    /**
     * Dynamic Properties
     * @var array
     */
    protected $dynamicProperties = [];

    /**
     * Set property on entity
     *
     * @param mixed $propertyName
     * @param mixed $value
     * @return void
     */
    public function __set($propertyName, $value)
    {
        if (in_array($propertyName, self::$protectedProperties)) {
            throw new \PrintNode\Exception\InvalidArgumentException($propertyName . ' is a protected property.');
        }

        $this->dynamicProperties[$propertyName] = $value;
    }

    /**
     * Get a dynamic property
     *
     * @param mixed $propertyName
     * @return mixed|null
     */
    public function __get($propertyName)
    {
        return $this->dynamicProperties[$propertyName] ?? null;
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
            $this->__set($key, $value);
        }

        return true;
    }

    /**
     * Implements the jsonSerialize method
     *
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $json = [];

        foreach ($this->dynamicProperties as $property => $value) {
            if (in_array($property, self::$protectedProperties)) {
                continue;
            }

            $json[$property] = $value;
        }

        return $json;
    }
}
