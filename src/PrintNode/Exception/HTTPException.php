<?php

namespace PrintNode\Exception;

class HTTPException extends \Exception
{
    
    /**
     * @var int HTTP status code
     */
    public $statusCode;

    /**
     * @var strubg Human readable error message the PrintNode API returned
     */
    public $message;

    /**
     * General HTTP Exception
     *
     * @param string HTTP method
     * @param string URL which triggered this Exception
     * @param int HTTP status code
     * @param strubg Human readable error message the PrintNode API returned
     */
    public function __construct($statusCode, $message)
    {
        
        $this->statusCode = $statusCode;
        $this->message = $message;

        parent::__construct(
            sprintf(
                'HTTP Error (%d): %s',
                $statusCode,
                $message
            )
        );
        
    }
    
}
