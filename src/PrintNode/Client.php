<?php

namespace PrintNode;

use PrintNode\Entity\Account;

if (!function_exists('curl_init')) {
    throw new \RuntimeException('Function curl_init() does not exist. Have you installed php curl?');
}

class Client
{
    
    /**
     * Stores a reference to a PrintNode\Credentials object
     * @var Credentials
     */
    private $credentials = null;
    
    /**
     * If set to true, the X-Pretty header will be sent to the PrintNode API, 
     * requesting human-readable JSON should be returned
     * @var bool
     */
    public $prettyJSON = false;
    
    /**
     * If set to true, the X-Dont-Log header will be sent to the PrintNode API,
     * @var bool
     */
    public $dontLog = false;
    
    /**
     * The API endpoint for requests made through the client can be overridden
     * here
     * @var string
     */
    public $apiHost;
    
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
     * Class Constructor
     * 
     * @param \PrintNode\Credentials $credentials A printnode credentials object
     */
    public function __construct(Credentials $credentials)
    {

        $this->credentials = $credentials;
                
    }

    /**
     * Get property on entity
     * 
     * @param mixed $propertyName
     * @return mixed
     */
    public function __get($propertyName)
    {
        
        if (!property_exists($this, $propertyName)) {
            throw new Exceptions\InvalidArgumentException(
                sprintf(
                    '%s does not have a property named %s',
                    get_class($this),
                    $propertyName
                )
            );
        }

        return $this->$propertyName;
    }
    
    /**
     * Makes a whoami request to the PrintNode API, returning a Whoami entity
     * describing the active account.
     * 
     * @return \PrintNode\Entity\Whoami
     */
    public function viewWhoAmI()
    {
     
        $request = new Request($this->credentials, 'whoami', 'GET');
        
        $response = $request->process();
        
        return $this->mapJsonToEntity($response->bodyJson, '\PrintNode\Entity\Whoami');
        
    }

    /**
     * Makes a credits request to the PrintNode API, returning the credit 
     * balance for the active account.
     * 
     * @return string
     */
    public function viewCredits()
    {
     
        $request = new Request($this->credentials, 'credits', 'GET');
        
        $response = $request->process();
        
        if ($response->httpStatusCode == '200') {
            return $response->body;
        }
        
        return false;
        
    }

    /**
     * Makes a computers request to the PrintNode API, returning an array
     * of all the registered computers on the active account.
     * 
     * @param int $offset The start index for the records the API should return
     * @param int $limit The number of records the API should return
     * @return bool|array
     */
    public function viewComputers($offset = 0, $limit = 500)
    {
    
        return $this->requestMapped('computers', 'GET', '\PrintNode\Entity\Computer');
        
    }

    /**
     * Makes a printers request to the PrintNode API, returning an array
     * of all the registered printers on the active account.
     * 
     * If a 'set' string or array of printer ids is passed to the third 
     * argument, the returned array will be filtered to just those printers.
     * 
     * If a 'set' string or array of computer ids is passed to the fourth 
     * argument, the returned array will be filtered to just those printers.
     * 
     * Both arguments can be combined to return only certain printers on certain
     * computers
     * 
     * @param int $offset The start index for the records the API should return
     * @param int $limit
     * @param string|array $printerSet
     * @param string|array $computerSet
     * @return bool|array
     */
    public function viewPrinters($offset = 0, $limit = 500, $printerSet = null, $computerSet = null)
    {
     
        if (isset($computerSet) && isset($printerSet)){
            $url = sprintf('computers/%s/printers/%s', $this->setImplode($computerSet), $this->setImplode($printerSet));
        } else if (isset($printerSet)) {
            $url = sprintf('printers/%s', $this->setImplode($printerSet));
        } else if (isset($computerSet)) {
            $url = sprintf('computers/%s/printers', $this->setImplode($computerSet));
        } else {
            $url = 'printers';
        }
        
        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);
        
        return $this->requestMapped($url, 'GET', '\PrintNode\Entity\Printer');
        
    }

    /**
     * Makes a printjobs request to the PrintNode API, returning an array
     * of all the printjobs that have been processed on the active account.
     * 
     * If a 'set' string or array of print job ids is passed to the third 
     * argument, the returned array will be filtered to just those print jobs.
     * 
     * If a 'set' string or array of printer ids is passed to the fourth 
     * argument, the returned array will be filtered to just those printers.
     * 
     * Both arguments can be combined to return only certain print jobs on 
     * certain printers
     * 
     * @param int $offset The start index for the records the API should return
     * @param int $limit
     * @param string|array $printJobSet
     * @param string|array $printerSet
     * @return bool|array
     */
    public function viewPrintJobs($offset = 0, $limit = 500, $printJobSet = null, $printerSet = null)
    {
        
        $url = 'printjobs';
            
        if (isset($printerSet) && isset($printJobSet)){
            $url = sprintf('printers/%s/printjobs/%s', $this->setImplode($printerSet), $this->setImplode($printJobSet));
        } else if (isset($printJobSet)) {
            $url = sprintf('printjobs/%s', $this->setImplode($printJobSet));
        } else if (isset($printerSet)) {
            $url = sprintf('printers/%s/printjobs', $this->setImplode($printerSet));
        }
        
        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);
        
        return $this->requestMapped($url, 'GET', '\PrintNode\Entity\PrintJob');
        
    }
    
    /**
     * Makes a printjobStatus request to the PrintNode API, returning an array
     * of all the printjobs that have been processed on the active account.
     * 
     * If a 'set' string or array of print job ids is passed to the third 
     * argument, the returned array will be filtered to just those print jobs.
     * 
     * Returned is an array of statuses in an array keyed by the print job id.
     * 
     * @param int $offset
     * @param int $limit
     * @param string|array $printJobSet
     * @return array
     */
    public function viewPrintJobStatus($offset = 0, $limit = 500, $printJobSet = null)
    {
        
        $url = 'printjobs/states';
                
        if (isset($printJobSet)) {
            $url = sprintf('printjobs/%s/states', $this->setImplode($printJobSet));
        }
        
        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);
        
        $response = $this->request($url, 'GET');
        
        if (!is_array($response->bodyJson)
            || (sizeof($response->bodyJson) == 0)) {
            return true;
        }
        
        $statuses = array();
        
        foreach ($response->bodyJson as $statusJson) {
            
            $jobStatus = $this->mapBodyJsonToEntityArray($statusJson, '\PrintNode\Entity\PrintJobState');
            
            $statuses[$jobStatus[0]->printJobId] = $jobStatus;
            
        }
        
        return $statuses;
        
    }
    
    /**
     * 
     * 
     * @param int $offset
     * @param int $limit
     * @param string|array $downloadSet
     * @return array
     */    
    public function viewApiKey($apiKey)
    {
        
        $url = sprintf('/account/apikey/%s', $apiKey);
        
        $request = new Request($this->credentials, $url, 'GET');
        
        $response = $request->process();
        
        //pndebug($response);
        
        /*
        $clientDownloads = array();
        
        foreach ($response->bodyJson as $clientDownloadJson) {
            
            $clientDownload = $this->mapJsonToEntity($clientDownloadJson, '\PrintNode\Entity\ClientDownload');
        
            $clientDownloads[$clientDownload->id] = $clientDownload;
            
        }
        
        return $clientDownloads;
        */
        
    }
    
    /**
     * 
     * 
     * @param int $offset
     * @param int $limit
     * @param string|array $downloadSet
     * @return array
     */    
    public function deleteApiKey($apiKey)
    {
        
        $url = sprintf('/account/apikey/%s', $apiKey);
        
        $request = new Request($this->credentials, $url, 'GET');
        
        $response = $request->process();
        
        //pndebug($response);
        
        /*
        $clientDownloads = array();
        
        foreach ($response->bodyJson as $clientDownloadJson) {
            
            $clientDownload = $this->mapJsonToEntity($clientDownloadJson, '\PrintNode\Entity\ClientDownload');
        
            $clientDownloads[$clientDownload->id] = $clientDownload;
            
        }
        
        return $clientDownloads;
        */
        
    }
    
    /**
     * Makes a viewScales request to the PrintNode API, returning an array
     * of all the scales filtered to the 
     * 
     * If a 'set' string or array of print job ids is passed to the third 
     * argument, the returned array will be filtered to just those print jobs.
     * 
     * Returned is an array of statuses in an array keyed by the print job id.
     * 
     * @param string $computerId
     * @param string $deviceName
     * @param string $deviceNumber
     * @return array
     */
    public function viewScales($computerId, $deviceName = null, $deviceNumber = null)
    {
        
        $url = sprintf('/computer/%s/scales', $computerId);
        
        if (isset($deviceName) && isset($deviceNumber)) {
            
            $url.= sprintf('/%s/%s', $deviceName, $deviceNumber);
            
        } else if (isset($deviceName)) {
            
            $url.= sprintf('/%s', $deviceName);
            
        }
        
        return $this->requestMapped($url, 'GET', '\PrintNode\Entity\Scale', 'deviceNum');
        
    }
    
    /**
     * Makes a viewClientDownloads request to the PrintNode API, returning an 
     * array of all the client downloads that are available on the active 
     * account.
     * 
     * If a 'set' string or array of download ids is passed to the third 
     * argument, the returned array will be filtered to just those downloads.
     * 
     * Returned is an array of downloads in an array keyed by the download id.
     * 
     * @param int $offset
     * @param int $limit
     * @param string|array $downloadSet
     * @return array
     */    
    public function viewClientDownloads($offset = 0, $limit = 1, $downloadSet = null)
    {
        
        $url = 'download/clients';
                
        if (isset($printJobSet)) {
            $url = sprintf('download/clients/%s', $this->setImplode($downloadSet));
        }
        
        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);
        
        return $this->requestMapped($url, 'GET', '\PrintNode\Entity\ClientDownload');
        
    }
    
    /**
     * Appends the offset and limit arguments to a given api endpoint url
     * 
     * @param string $url
     * @param int $offset
     * @param int $limit
     * @return string
     * @throws \PrintNode\Exception\InvalidArgumentException
     */
    public function applyLimitOffsetToUrl($url, $offset, $limit)
    {
        
        if (!\is_numeric($offset)) {
            throw new \PrintNode\Exception\InvalidArgumentException('Offset must be a number');
        }
        
        if (!\is_numeric($limit)) {
            throw new \PrintNode\Exception\InvalidArgumentException('Limit must be a number');
        }
        
        if ($offset < 0) {
            throw new \PrintNode\Exception\InvalidArgumentException('Offset cannot be negative');
        }
        
        if ($limit < 1) {
            throw new \PrintNode\Exception\InvalidArgumentException('Limit must be greater than zero');
        }
        
        return sprintf('%s?offset=%s&limit=%s', $url, $offset, $limit);
        
    }
    
    /**
     * Sends a printJob to the PrintNode API.
     * 
     * @param \PrintNode\Entity\PrintJob $printJob
     * @param bool $returnObject If set to true, returns the full printjob data
     * @return |PrintNode\Entity\PrintJob|string
     */
    public function createPrintJob($printJob, $returnObject = false)
    {
        
        $response = $this->request('printjobs', 'POST', \json_encode($printJob));
        
        if ($returnObject) {
            return $this->viewPrintJobs(0, 1, $response->body);
        }
        
        return $response->body;
        
    }
    
    /**
     * Creates a child account
     *
     * @param \PrintNode\Entity\Account $account
     * @param array $tags
     * @param array $apiKeys
     */
    public function createChildAccount(\PrintNode\Entity\Account $account, $apiKeys = null, $tags = null)
    {
    
        $newChildAccount = new \PrintNode\Entity\ChildAccount($this);
        
        $newChildAccount->addAccount($account);
        
        if (is_array($apiKeys)) {
            foreach ($apiKeys as $apiKey) {
                $newChildAccount->ApiKeys[] = $apiKey;
            }
        }
        
        if (is_array($tags)) {
            foreach ($tags as $tagname => $tag) {
                $newChildAccount->Tags[$tagname] = $tag;
            }
        }
        
        $response = $this->request('account', 'POST', \json_encode($newChildAccount));
        
        $newChildAccount->Account->mapValuesFromJson($response->bodyJson->Account);
        
        $newChildAccount->ApiKeys = array();
        $newChildAccount->Tags = array();
        
        if ($response->bodyJson->ApiKeys instanceof \stdClass) {
            foreach ($response->bodyJson->ApiKeys as $apiDescription => $apiKey) {
                $newChildAccount->ApiKeys[$apiDescription] = $apiKey;
            }
        }
        
        if ($response->bodyJson->Tags instanceof \stdClass) {
            foreach ($response->bodyJson->Tags as $tagDescription => $tag) {
                $newChildAccount->Tags[$tagDescription] = $tag;
            }
        }
        
        return $newChildAccount;
        
    }
    
    /**
     * Makes a delete account request to the PrintNode API for the active
     * child account.
     * 
     * @return bool
     */
    public function deleteChildAccount()
    {
        
        $response = $this->request('account', 'DELETE');
        
        return $response->bodyJson;
        
    }
    
    /**
     * Makes a create tag request to the PrintNode API, creating the tag
     * specified in argument 1 with the value specified in argument 2 on the 
     * active account.
     * 
     * @param string $tagName The tag name to create
     * @param string $tagValue The value of the tag to create
     * @return string
     */
    public function createTag($tagName, $tagValue)
    {
        
        $response = $this->request('account/tag/' . $tagName, 'POST', \json_encode($tagValue));
        
        return $response->bodyJson;
        
    }
    
    /**
     * Makes a create tag request to the PrintNode API, returning the tag 
     * specified in the first argument on the active account.
     * 
     * @param string $tagName
     * @return string
     */
    public function viewTag($tagName)
    {
        
        $response = $this->request('account/tag/' . $tagName, 'GET');
        
        return $response->bodyJson;
        
    }
    
    /**
     * Makes a delete tag request to the PrintNode API, removing the tag 
     * specified in the first argument on the active account.
     * 
     * @param string $tagName
     * @return string
     */
    public function deleteTag($tagName)
    {
        
        $response = $this->request('account/tag/' . $tagName, 'DELETE');
        
        return $response->bodyJson;
        
    }
    
    /**
     * If a string is passed as an argument, it returns the string. If an array
     * is passed, the array is imploded into a comma-seperated string.
     * 
     * @param string|array $set
     * @return string
     */
    public function setImplode($set)
    {
        
        if (is_array($set)) {
            return implode(',', $set);
        }
        
        return $set;
        
    }
    
    /**
     * Makes a request to the PrintNode API, using the Request object.
     * 
     * @param string $url The API service endpoint
     * @param string $method The HTTP request method to use
     * @return Response
     */
    public function request($url, $method, $content = null)
    {
        
        $request = new Request($this->credentials, $url, $method);
        
        $request->setPropertiesFromClient($this);
        
        if ($content !== null) {
            $request->body = $content;
        }
        
        return $request->process();
        
    }
    
    /**
     * Makes a request to the PrintNode API and attempts to map the response
     * to a particular entity type.
     * 
     * @param string $url The API service endpoint
     * @param string $method The HTTP request method to use
     * @param string $responseEntity The name of the entity to return
     * @param string $keyname The entity property to use as the return array key
     * @return mixed
     */
    public function requestMapped($url, $method, $responseEntity = null, $keyname = 'id')
    {
        
        $response = $this->request($url, $method);
        
        if (!is_array($response->bodyJson)
            || (sizeof($response->bodyJson) == 0)) {
            return true;
        }
        
        if ($responseEntity === null ) {
            
            return $response->bodyJson;
            
        } else {
            
            return $this->mapBodyJsonToEntityArray($response->bodyJson, $responseEntity, $keyname);
            
        }
        
    }
    
    /**
     * Maps an array of JSON objects to map to an array of entities of a given
     * type
     * 
     * @param string $json The json to map to entities
     * @param string $responseEntity The name of the entity to return
     * @param string $keyname The entity property to use as the return array key
     * @return \PrintNode\responseEntity
     * @throws Exception\InvalidArgumentException
     */
    public function mapBodyJsonToEntityArray($json, $responseEntity, $keyname = null)
    {
        
        $items = array();
        
        foreach ($json as $itemIndex => $itemJson) {

            $item = $this->mapJsonToEntity($itemJson, $responseEntity);

            $key = $itemIndex;
            
            if ($keyname) {
                
                if (!isset($itemJson->$keyname)) {
                    throw new Exception\InvalidArgumentException('Supplied keyname is not present in entity');
                }
                
                $key = $item->$keyname;
                
            }

            $items[$key] = $item;

        }

        return $items;
        
    }
    
    /**
     * Converts json to a given entity type
     * 
     * @param string $json The json to map to entities
     * @param type $responseEntity
     */
    public function mapJsonToEntity($json, $responseEntity)
    {
        
        $item = new $responseEntity($this);

        $item->mapValuesFromJson($json);
        
        return $item;
        
    }
    
}
