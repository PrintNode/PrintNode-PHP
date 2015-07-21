<?php

namespace PrintNode;

/**
 * Client
 *
 * Object representing a Client in PrintNode API
 *
 * @property-read int $id
 * @property-read bool $enabled
 * @property-read string $edition
 * @property-read string $version
 * @property-read string $os
 * @property-read string $filename
 * @property-read string $filesize
 * @property-read string $sha1
 * @property-read DateTime $releaseTimestamp
 * @property-read string $url
 */
class Client extends Entity
{
    protected $id;
    protected $enabled;
    protected $edition;
    protected $version;
    protected $os;
    protected $filename;
    protected $filesize;
    protected $sha1;
    protected $releaseTimestamp;
    protected $url;

    public function formatForPatch()
    {
        return json_encode(array("enabled" => $this->enabled));
    }

    public function endPointUrlArg()
    {
        return (string)$this->id;
    }

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}
