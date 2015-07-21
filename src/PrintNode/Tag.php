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
        return $this->name;
    }

	public function formatForPost()
	{
		return json_encode($this->value);
	}

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}
