<?php

namespace PrintNode;

use PrintNode\HTTPException;

/**
 * Response
 *
 * HTTP response object.
 */
class Response
{

    /**
     * Original Request HTTP Method
     * @var string
     */
    private $method;

    /**
     * Original Request URL
     * @var string
     */
    private $url;

    /**
     * Response headers
     * @var mixed[]
     */
    private $headers;

    /**
     * Response body
     * @var string
     */
    private $content;

    /**
     * Constructor
     * @param mixed $method
     * @param mixed $url
     * @param mixed $content
     * @param mixed $headers
     * @return Response
     */
    public function __construct($method, $url, $content, array $headers)
    {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->content = $content;
    }

    /**
     * Get request method
     * @param void
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get Request URL
     * @param void
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get Response body
     * @param void
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get Response headers
     * @param void
     * @return mixed[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get Response body decoded into an array
     *
     * @param void
     * @return mixed
     */
    public function getDecodedContent($asArray = false)
    {
        $decoded = json_decode($this->content, (bool) $asArray);
        // have error?
        if (null === $decoded and JSON_ERROR_NONE !== $lastError = json_last_error()) {
            $message = sprintf(<<<TEXT
PrintNode API did not return valid JSON for request %s %s.

--- BEGIN SERVER RESPONSE ---
%s
--- END SERVER RESPONSE ---
TEXT
                , $this->method,
                $this->url,
                $this->content
            );
            throw new \RuntimeException($message);
        }
        return $decoded;
    }

    /**
     * Return <true> if response code is 2xx
     * @return boolean
     */
    public function isOK ()
    {
        $statusCode = $this->getStatusCode();
        return $statusCode >= 200 and $statusCode < 300;
    }

    public function getHTTPException ()
    {
        if ($this->isOK()) {
            return new \RuntimeException("HTTP response {$this->getStatusCode()}. No HTTPException to throw");
        }
        return new HTTPException(
            $this->getMethod(),
            $this->getUrl(),
            $this->getStatusCode(),
            $this->getStatusMessage()
        );
    }

    /**
     * Get HTTP status code
     * @param void
     * @return string
     */
    public function getStatusCode()
    {
        $status = $this->getStatus();
        return $status['code'];
    }

    /**
     * Get HTTP status code
     * @param void
     * @return string
     */
    public function getStatusMessage()
    {
        $status = $this->getStatus();
        return $status['message'];
    }

    /**
     * Extract the HTTP status code and message
     * from the Response headers
     * @param void
     * @return mixed[]
     */
    private function getStatus()
    {
        if (!($statusArray = preg_grep('/^HTTP\/(1.0|1.1)\s+(\d+)\s+(.+)/', $this->headers))) {
            throw new \RuntimeException('Could not determine HTTP status from API response');
        }

        if (!preg_match('/^HTTP\/(1.0|1.1)\s+(\d+)\s+(.+)/', $statusArray[0], $matchesArray)) {
            throw new \RuntimeException('Could not determine HTTP status from API response');
        }

        try {
            $response = $this->getDecodedContent(true);
        } catch (\RuntimeException $exception) {
            $message = $matchesArray[3];
        }

        // human readable exceptions
        return array(
            'code' => (int) $matchesArray[2],
            'message' => isset($response['message']) ? $response['message'] : $matchesArray[3],
        );
    }

}
