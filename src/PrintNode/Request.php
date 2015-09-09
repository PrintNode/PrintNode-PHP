<?php

namespace PrintNode;

class Request
{
    
    /**
     * Reference to a credentials object
     * @var Credentials
     */
    private $credentials;
    
    /**
     * Service to which the request will be made
     * @var string
     */
    private $service;
    
    /**
     * Method to use for the request
     * @var string
     */
    private $method;
    
    /**
     * The host to make this request to
     * @var string
     */
    public $apiHost = 'api.printnode.com';
    
    /**
     * If set, requests that the responding JSON should be formatted for human
     * readability
     * @var bool 
     */
    public $prettyJSON = false;
    
    /**
     * If set, requests that this API request should not be logged.
     * @var bool
     */
    public $dontLog = false;
    
    /**
     * Stores any additional arbitary headers that should be sent with the 
     * request
     * @var array
     */
    public $additionalHeaders = array();
    
    /**
     * The body of the request to be sent
     * @var string
     */
    public $body;
    
    /**
     * Request logging callback
     * @var function
     */
    public $requestLogMethod;
    
    /**
     * Response logging callback
     * @var function
     */
    public $responseLogMethod;
    
    /**
     * Class constructor
     * 
     * @param \PrintNode\Credentials $credentials
     * @param string $service
     * @param string $method
     */
    public function __construct(Credentials $credentials, $service, $method)
    {

        $this->credentials = $credentials;
        $this->setService($service);
        $this->setMethod($method);
        
    }
    
    /**
     * Sets the service property
     * 
     * @param string $service The service to set
     * @return boolean
     */
    protected function setService($service)
    {
        
        $this->service = $service;
        
        return true;
        
    }
    
    /**
     * Sets the request property
     * 
     * @param string $method The request method to set
     * @return boolean
     */
    protected function setMethod($method)
    {
                
        $this->method = $method;
        
        return true;
        
    }
    
    /**
     * Loads in overriding properties from the client
     * 
     * @param Client $client
     * @return boolean
     */
    public function setPropertiesFromClient($client)
    {
                
        if ($client->prettyJSON) {
            $this->prettyJSON = (bool)$client->prettyJSON;
        }
        
        if ($client->dontLog) {
            $this->dontLog = (bool)$client->dontLog;
        }
        
        if (is_array($client->additionalHeaders)
            && (sizeof($client->additionalHeaders) > 0)) {
            $this->additionalHeaders = $client->additionalHeaders;
        }
        
        if ($client->apiHost) {
            $this->apiHost = $client->apiHost;
        }
        
        return true;
        
    }
    
    /**
     * Returns the URL to make the request to
     * 
     * @return string
     */
    public function getUrl()
    {
        
        if (!$this->service) {
            throw new Exception\RuntimeException('Service not set');
        }
        
        return 'https://' . $this->apiHost . '/' . $this->service;
        
    }
    
    /**
     * Returns the authentication headers for a request
     * 
     * @return array
     */
    public function getCredentialHeader()
    {
        
        if (mb_strlen(trim($this->credentials->apiKey))) {
            $headers = array(
                'Authorization: Basic ' . \base64_encode($this->credentials->apiKey . ':'),
            );
        } else {

            $headers = array(
                'Authorization: Basic ' . \base64_encode($this->credentials->username . ':' . $this->credentials->password),
                'X-Auth-With-Account-Credentials: true',
            );

        }
        
        if ($childAccountHeaders = $this->getChildAccountHeaders()) {
            $headers = \array_merge($headers, $childAccountHeaders);
        }
        
        return $headers;

    }
    
    /**
     * Returns child account authentication request headers, if set
     * 
     * @return array
     */
    public function getChildAccountHeaders()
    {
        
        if ($this->credentials->childAccountEmail) {
            
            return array(sprintf('X-Child-Account-By-Email: %s',
                                 $this->credentials->childAccountEmail));
            
        } else if ($this->credentials->childAccountCreatorRef) {
            
            return array(sprintf('X-Child-Account-By-CreatorRef: %s',
                                 $this->credentials->childAccountCreatorRef));
            
        } else if ($this->credentials->childAccountId) {
            
            return array(sprintf('X-Child-Account-By-Id: %s',
                                 $this->credentials->childAccountId));
            
        }
        
        return false;
        
    }
    
    /**
     * Returns a full set of authentication headers
     * 
     * @return array
     */
    public function getHeaders()
    {
        
        // The 'Expect:' header is required to prevent the server responding 
        // with '100 Continue' headers
        $headers = array(
            'Content-Type: application/json',
            'Expect:',
        );
        
        if ($this->prettyJSON) {
            $headers[] = 'X-Pretty: 1';
        }
        
        if ($this->dontLog) {
            $headers[] = 'X-Dont-Log: 1';
        }
        
        return \array_merge($headers, $this->getCredentialHeader());
        
    }
    
    /**
     * Makes a curl request and returns a response object
     * 
     * @return \PrintNode\Response
     */
    public function process()
    {
        
        $response = $this->curlExec($this->method, $this->getUrl(), $this->getHeaders());
        
        return new Response($response);
        
    }
    
    /**
     * Makes a curl request.
     * 
     * @param string $method Request method 
     * @param string $url The url to make the request to
     * @param string $headers Request headers to send
     * @return string
     * @throws Exception\RuntimeException
     */
    protected function curlExec ($method, $url, $headers)
    {

        $curlHandle = $this->getCurlHandle();
        
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlHandle, CURLOPT_URL, $url);        
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->getHeaders());
        
        if ($this->body) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, (string) $this->body);
        }
        
        $response = @curl_exec($curlHandle);
        
       $curlInfo = curl_getinfo($curlHandle);
        
        if ($response === false) {
            throw new Exception\RuntimeException(
                sprintf(
                    'cURL Error (%d): %s',
                    curl_errno($curlHandle),
                    curl_error($curlHandle)
                )
            );
        }
        
        return $response;
                
    }
    
    /**
     * Returns an instance of the curl handle 
     * 
     * @return mixed
     */
    protected function getCurlHandle()
    {
        
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);
        
        return $curlHandle;
        
    }
    
}
