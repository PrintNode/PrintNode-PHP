<?php

namespace PrintNode;

/**
 * ApiKey
 *
 * Object representing an ApiKey for POST in PrintNode API
 *
 * @property-read string $description
 */
class ApiKey extends Entity
{
    protected $description;

    public function endPointUrlArg()
    {
        return $this->description;
    }

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}
