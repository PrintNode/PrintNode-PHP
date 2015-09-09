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
    
    /**
     * The client download id
     * @var int
     */
    protected $id;
    
    /**
     * The flag that indicates if this download is currently available
     * @var bool
     */
    protected $enabled;
    
    /**
     * The edition of the client download
     * @var string
     */
    protected $edition;
    
    /**
     * The client version
     * @var string
     */
    protected $version;
    
    /**
     * The operating system for which this client download is intended
     * @var string
     */
    protected $os;
    
    /**
     * The filename for this client installer
     * @var string
     */
    protected $filename;
    
    /**
     * The file size of the client installer
     * @var string
     */
    protected $filesize;
    
    /**
     * SHA1 fingerprint for the client installer
     * @var string
     */
    protected $sha1;
    
    /**
     * The timestamp for the client installer release
     * @var int
     */
    protected $releaseTimestamp;
    
    /**
     * The URL at which the client installer can be downloaded
     * @var int
     */
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
