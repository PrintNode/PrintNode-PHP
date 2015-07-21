<?php

namespace PrintNode;

/**
 * Download
 *
 * Object representing a Download Client in PrintNode API
 *
 *
 * @property-read string $edition
 * @property-read string $version
 * @property-read string $os
 * @property-read string $filename
 * @property-read string $filesize
 * @property-read string $sha1
 * @property-read DateTime $releaseTimestamp
 * @property-read string $url
 */


class Download extends Entity
{
    protected $edition;
    protected $version;
    protected $os;
    protected $filename;
    protected $filesize;
    protected $sha1;
    protected $releaseTimestamp;
    protected $url;

    public function foreignKeyEntityMap()
    {
        return array(
        );
    }
}
