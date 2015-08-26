<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

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
class ClientDownload extends Entity
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
    
    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'id' => null,
        'enabled' => null,
        'edition' => null,
        'version' => null,
        'os' => null,
        'filename' => null,
        'filesize' => null,
        'sha1' => null,
        'releaseTimestamp' => null,
        'url' => null,
    );
    
}
