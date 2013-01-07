<?php

/**
 * PrintNode_Computer
 *
 * Object representing a Computer in PrintNode API
 *
 * @property-read int $id
 * @property string $name
 * @property string $inet
 * @property string $inet6
 * @property string $version
 * @property string $jre
 * @property object $systemInfo
 * @property boolean $acceptOfflinePrintJobs
 * @property DateTime $createTimestamp
 */
class PrintNode_Computer extends PrintNode_Entity
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