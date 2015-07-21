<?php

namespace PrintNode;

/**
 * Tag
 *
 * Object representing a Tag for POST in PrintNode API
 *
 * @property-read String $name
 * @property-read String $value
 */
class Tag extends Entity
{
    protected $name;
    protected $value;

    public function endPointUrlArg()
    {
        return $name;
    }

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}
