<?php

namespace PrintNode;

/**
 * PrintNode_Computer
 *
 * Object representing a Computer in PrintNode API
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $inet
 * @property-read string $inet6
 * @property-read string $version
 * @property-read string $jre
 * @property-read object $systemInfo
 * @property-read boolean $acceptOfflinePrintJobs
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class Computer extends Entity
{
    protected $id;
    protected $name;
    protected $inet;
    protected $inet6;
    protected $hostname;
    protected $version;
    protected $jre;
    protected $systemInfo;
    protected $acceptOfflinePrintJobs;
    protected $createTimestamp;
    protected $state;

    /**
     * @inheritdoc
     */
    public function foreignKeyEntityMap()
    {
        return array();
    }
}
