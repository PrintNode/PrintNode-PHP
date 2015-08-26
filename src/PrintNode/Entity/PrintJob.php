<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * PrintJob
 *
 * Object representing a PrintJob in PrintNode API
 *
 * @property-read int $id
 * @property Printer $printer
 * @property string $title
 * @property string $contentType
 * @property string $content
 * @property string $source
 * @property-read int $filesize
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class PrintJob extends Entity
{
    
    protected $id;
    protected $printer;
    protected $printerId;
    protected $title;
    protected $contentType;
    protected $content;
    protected $source;
    protected $filesize;
    protected $options;
    protected $expireAfter;
    protected $qty;
    protected $authentication;
    protected $createTimestamp;
    protected $state;
    protected $expireAt;

    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'id' => null,
        'printer' => '\PrintNode\Entity\Printer',
        'title' => null,
        'contentType' => null,
        'content' => null,
        'source' => null,
        'filesize' => null,
        'options' => null,
        'expireAfter' => null,
        'qty' => null,
        'authentication' => null,
        'createTimestamp' => null,
        'state' => null,
        'expireAt' => null,
    );
    
    public static $validOptions = array(
        'bin',
        'collate',
        'copies',
        'dpi',
        'duplex',
        'fit_to_page',
        'media',
        'nup',
        'pages',
        'paper',
        'rotate',
    );
    
    public static $requried = array(
        array (
            'printer',
            'printerId',
        ),
        'title',
        'contentType',
        'content',
        'source',
    );
    
    public function addBase64File($filePath)
    {
        
        if (!\file_exists($filePath)) {
            throw new \PrintNode\Exception\InvalidArgumentException('File does not exist: ' . $filePath);
        }
        
        if (!($contents = \file_get_contents($filePath))) {
            throw new \PrintNode\Exception\InvalidArgumentException('Could not open file: ' . $filePath);
        }
        
        $this->content = \base64_encode($contents);
                
        return true;
        
    }
    
    public function addPdfFile($filePath)
    {
        
        $this->addBase64File($filePath);
        
        return true;
        
    }
    
    public function addRawFile($filePath)
    {
        
        $this->addBase64File($filePath);
        
        return true;
        
    }
    
}
