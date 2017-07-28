<?php

namespace PrintNode;

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
 * @property array $options
 * @property int $expireAfter
 * @property int $qty
 * @property-read int $expireAt
 * @property-read int $filesize
 * @property-read DateTime $createTimestamp
 * @property-read string $state
 */
class PrintJob extends Entity
{
    protected $id;
    protected $printer;
    protected $title;
    protected $contentType;
    protected $content;
    protected $source;
    protected $options;
    protected $expireAfter;
    protected $qty;
    protected $expireAt;
    protected $filesize;
    protected $createTimestamp;
    protected $state;



    public function foreignKeyEntityMap()
    {
        return array(
            'printer' => 'PrintNode\Printer'
        );
    }
}
