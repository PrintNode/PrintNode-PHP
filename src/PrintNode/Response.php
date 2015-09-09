<?php

namespace PrintNode;

class Response
{

    /**
     * HTTP version of the response
     * @var string
     */
    public $httpVersion;
    
    /**
     * HTTP status code of the response
     * @var string
     */
    public $httpStatusCode;
    
    /**
     * HTTP status message for the response
     * @var string
     */
    public $httpStatusMessage;
    
    /**
     * Array of the headers returned in the response
     * @var array
     */
    public $headers;
    
    /**
     * Response body string
     * @var string
     */
    public $body;
    
    /**
     * Response body as a Json object
     * @var mixed
     */
    public $bodyJson;
    
    /**
     * Class constructor
     * 
     * @param string $responseString
     */
    public function __construct($responseString)
    {
    
        $this->processResponseString($responseString);
        
    }
    
    /**
     * Processes the response string into headers and body, then converts the
     * body response into a json object
     * 
     * @param string $responseString The full response string
     * @return boolean
     * @throws Exception\RuntimeException
     * @throws Exception\HTTPException
     */
    public function processResponseString($responseString)
    {
        
        $responseArray = explode("\r\n", $responseString);
        
        $this->body = \array_pop($responseArray);
        
        foreach ($responseArray as $index => $responseItem) {
            
            if (mb_strlen(trim($responseItem)) == 0) {
                continue;
            }
            
            if ($index == 0) {
                
                $statusCodeString = $responseItem;
            
                preg_match('/^HTTP\/(1.0|1.1)\s+(\d+)\s+(.+)/', $statusCodeString, $statusCodeArray);
                
                if (!sizeof($statusCodeArray) == 4) {
                    throw new Exception\RuntimeException('Could not determine HTTP status from API response');
                }
                
                $this->httpVersion = $statusCodeArray[1];
                $this->httpStatusCode = $statusCodeArray[2];
                $this->httpStatusMessage = $statusCodeArray[3];
                
            } else {
             
                $headerKey = trim(mb_substr($responseItem, 0, mb_strpos($responseItem, ':')));
                
                $this->headers[$headerKey] = trim(mb_substr($responseItem, mb_strpos($responseItem, ':') + 1));
                
            }
            
        }
        
        if (isset($this->headers['Content-Length'])) {
            $this->body = mb_substr($responseString, ($this->headers['Content-Length'] * -1), $this->headers['Content-Length']);
        }
        
        if (mb_strlen(trim($this->body)) > 0) {
            $this->processBodyJson($this->body);
        }
        
        if ($this->httpStatusCode == 429) {
            
            throw new Exception\RateLimitException($this->bodyJson->message);
            
        } else if (($this->httpStatusCode < 200)
            || ($this->httpStatusCode >= 300)) {
            
            throw new Exception\HTTPException($this->httpStatusCode, $this->bodyJson->message);
            
        }
        
        return true;
        
    }
        
    /**
     * Process the response body into a JSON object and places it into the 
     * bodyJson property
     * 
     * @param string $bodyJson The full response string
     * @return boolean
     * @throws \PrintNode\Exception\RuntimeException
     */
    public function processBodyJson($bodyJson)
    {
        
        $decoded = json_decode($bodyJson);
        
        if (!json_last_error()) {
            $this->bodyJson = $decoded;
            return true;
        }
        
        switch (json_last_error()) {
            
            case JSON_ERROR_DEPTH:
                $errorCode = 'Maximum stack depth exceeded';
            break;
        
            case JSON_ERROR_STATE_MISMATCH:
                $errorCode = 'Underflow or the modes mismatch';
            break;
        
            case JSON_ERROR_CTRL_CHAR:
                $errorCode = 'Unexpected control character found';
            break;
        
            case JSON_ERROR_SYNTAX:
                $errorCode = 'Syntax error, malformed JSON';
            break;
        
            case JSON_ERROR_UTF8:
                $errorCode = 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        
            default:
                $errorCode = 'Unknown error';
            break;
                
        }
        
        throw new \PrintNode\Exception\RuntimeException('JSON Error - '. $errorCode);

    }
    
}