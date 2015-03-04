<?php

namespace PrintNode;

/**
 * PrintNode_PrintJob
 *
 * Object representing a PrintJob in PrintNode API
 *
 * @property-read int $id
 * @property PrintNode_Printer $printer
 * @property string $title
 * @property string $contentType
 * @property string $content
 * @property string $source
 * @property-read int $filesize
 * @property-read \DateTime $createTimestamp
 * @property-read string $state
 */
class PrintNode_PrintJob extends PrintNode_Entity
{
    protected $id;
    protected $printer;
    protected $title;
    protected $contentType;
    protected $content;
    protected $source;
    protected $filesize;
    protected $createTimestamp;
    protected $state;

    public function foreignKeyEntityMap()
    {
        return array(
            'printer' => 'PrintNode\PrintNode_Printer'
        );
    }
}