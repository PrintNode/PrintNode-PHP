<?php

namespace PrintNode;

/**
 * Whoami
 *
 * Object representing a Whoami request in PrintNode API
 *
 * @property-read int $id
 * @property-read string $firstname
 * @property-read string $lastname
 * @property-read string $email
 * @property-read bool $canCreateSubAccounts
 * @property-read string $creatorEmail
 * @property-read string $creatorRef
 * @property-read int[] $childAccounts
 * @property-read string $credits
 * @property-read int $numComputers
 * @property-read int $totalprints
 * @property-read string[] $versions
 * @property-read int[] $connected
 * @property-read string[] $Tags
 * @property-read string[] $ApiKeys
 * @property-read string $state
 * @property-read string[] $permissions
 */
class Whoami extends Entity
{
    protected $id;
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $canCreateSubAccounts;
    protected $creatorEmail;
    protected $creatorRef;
    protected $childAccounts;
    protected $credits;
    protected $numComputers;
    protected $totalPrints;
    protected $versions;
    protected $connected;
    protected $Tags;
    protected $ApiKeys;
    protected $state;
    protected $permissions;

    public function foreignKeyEntityMap()
    {
        return array();
    }
}
