<?php

namespace PrintNode;

/**
 * Account
 *
 * Object representing an Account to POST in PrintNode API
 *
 * @property-read string[string] $Account
 * @property-read string[] $ApiKeys
 * @property-read string[string] $Tags
 */
class Account extends Entity
{
    protected $Account;
    protected $ApiKeys;
    protected $Tags;

    public function formatForPatch()
    {
        return json_encode($this->Account);
    }

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}
