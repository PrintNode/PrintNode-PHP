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
    
    /**
     * The print job id
     * @var int
     */
    protected $id;
    
    /**
     * The name of the printer 
     * @var string
     */
    protected $printer;
    
    /**
     * The id of the printer
     * @var int
     */
    protected $printerId;
    
    /**
     * The title of the print job
     * @var string
     */
    protected $title;
    
    /**
     * The content type of this print job
     * @var string
     */
    protected $contentType;
    
    /**
     * The content of the printjob, either a URL or base 64 encoded data
     * @var string 
     */
    protected $content;
    
    /**
     * A string that describes the origin of the printjob
     * @var string
     */
    protected $source;
    
    /**
     * The size of the file that is being printed
     * @var string
     */
    protected $filesize;
    
    /**
     * An associative array of any print job options to be sent
     * @var array
     */
    protected $options;
    
    /**
     * The number of seconds for which PrintNode should attempt to reprint
     * this job in the event that it can't print immeadiately.
     * @var int
     */
    protected $expireAfter;
    
    /**
     * The number of copies to print
     * @var int
     */
    protected $qty;
    
    /**
     * An associative array of any HTTP credentials required to print a file
     * located at a remote url
     * @var array 
     */
    protected $authentication;
    
    /**
     * The timestamp for the creation of the print job
     * @var string
     */
    protected $createTimestamp;
    
    /**
     * The current state of the printjob
     * @var string
     */
    protected $state;
    
    /**
     * The time at which this printjob expires
     * @var string 
     */
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
    
    /**
     * Valid option keys for the options property
     * @var array
     */
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
    
    /**
     * Fields that are required when this object is sent as a request
     * @var array
     */
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
    
    /**
     * Adds a file to the object, base 64 encoding it in the process
     * 
     * @param string $filePath The path to the file to be added
     * @return boolean
     * @throws \PrintNode\Exception\InvalidArgumentException
     */
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
    
    /**
     * Adds a base64 encoded pdf file
     * 
     * @param string $filePath The path to the PDF file to be added
     * @return boolean
     */
    public function addPdfFile($filePath)
    {
        
        $this->addBase64File($filePath);
        $this->contentType = 'pdf_base64';
        
        return true;
        
    }
    
    /**
     * Adds a base64 encoded raw file
     * 
     * @param string $filePath The path to the RAW file to be added
     * @return boolean
     */
    public function addRawFile($filePath)
    {
        
        $this->addBase64File($filePath);
        $this->contentType = 'raw_base64';
        
        return true;
        
    }
    
}
