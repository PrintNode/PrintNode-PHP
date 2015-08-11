<?php

namespace PrintNode;

class HTTPException extends \Exception
{
    /**
     * @var string HTTP method
     */
    public $method;

    /**
     * @var string URL which triggered this Exception
     */
    public $url;

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
    public function __construct($method, $url, $statusCode, $message)
    {
        $this->method = $method;
        $this->url = $url;
        $this->statusCode = $statusCode;
        $this->message = $message;

        parent::__construct(
            sprintf(
                '%s %s - HTTP Error (%d): %s',
                $method,
                $url,
                $statusCode,
                $message
            )
        );
    }

}
