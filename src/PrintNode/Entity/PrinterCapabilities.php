<?php

namespace PrintNode\Entity;

use PrintNode\Entity;

/**
 * Printer
 *
 * Object representing printer capabilities in PrintNode API
 *
 * @property-read array $bins;
 * @property-read bool $collate;
 * @property-read int $copies;
 * @property-read bool $color;
 * @property-read string $dpis;
 * @property-read array $extent;
 * @property-read array $medias;
 * @property-read array $nup;
 * @property-read array $papers;
 * @property-read array $printrate;
 * @property-read bool $supports_custom_paper_size;
 * 
 */
class PrinterCapabilities extends Entity
{
    
    protected $bins;
    protected $collate;
    protected $copies;
    protected $color;
    protected $dpis;
    protected $extent;
    protected $medias;
    protected $nup;
    protected $papers;
    protected $printrate;
    protected $supports_custom_paper_size;
    
    /**
     * Response map for converting this entity back and forth from JSON objects
     * @var array
     */
    public static $responseMap = array(
        'bins' => null,
        'collate' => null,
        'copies' => null,
        'color' => null,
        'dpis' => null,
        'extent' => null,
        'medias' => null,
        'nup' => null,
        'papers' => 'PrintNode\Entity\PrinterCapabilities\Papers',
        'printrate' => null,
        'supports_custom_paper_size' => null,
    );
    
}
