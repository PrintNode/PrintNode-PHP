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
     * Stores any additional arbitary headers that should be sent with the
     * request
     * @var array
     */
    public $additionalHeaders = array();

    /**
     * The API endpoint for requests made through the client can be overridden
     * here
     * @var string
     */
    public $apiHost;

    /**
     * Stores the last request object processed
     * @var \PrintNode\Response
     */
    public $lastRequest;

    /**
     * Stores the last response object processed
     * @var \PrintNode\Response
     */
    public $lastResponse;

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

        $this->lastResponse = $this->makeRequest('whoami', 'GET');

        return $this->mapJsonToEntity($this->lastResponse->bodyJson, '\PrintNode\Entity\Whoami');

    }

    /**
     * Makes a credits request to the PrintNode API, returning the credit
     * balance for the active account as a string
     *
     * @return string
     */
    public function viewCredits()
    {

        $this->lastResponse = $this->makeRequest('credits', 'GET');

        return $this->lastResponse->bodyJson;

    }

    /**
     * Makes a computers request to the PrintNode API, returning an array
     * of all the registered computers on the active account.
     *
     * @param int $offset (Optional) The start index for the records the API should return
     * @param int $limit (Optional) The number of records the API should return
     * @param string|array $computerSet (Optional) 'set' string or array of computer ids to which the response should be limited
     * @return \PrintNode\Entity\Computer[]
     */
    public function viewComputers($offset = 0, $limit = 500, $computerSet = null)
    {

        if (isset($computerSet)) {
            $url = sprintf('computers/%s', $this->setImplode($computerSet));
        } else {
            $url = 'computers';
        }

        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);

        return $this->makeRequestMapped($url, 'GET', '\PrintNode\Entity\Computer');

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
     * @param int $offset (Optional) The start index for the records the API should return
     * @param int $limit (Optional) The number of records the API should return
     * @param string|array $printerSet (Optional) 'set' string or array of printer ids to which the response should be limited
     * @param string|array $computerSet (Optional) 'set' string or array of computer ids to which the response should be limited
     * @return \PrintNode\Entity\Printer[]
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

        return $this->makeRequestMapped($url, 'GET', '\PrintNode\Entity\Printer');

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
     * @param int $offset (Optional) The start index for the records the API should return
     * @param int $limit (Optional) The number of records the API should return
     * @param string|array $printJobSet (Optional) 'set' string or array of print job ids to which the response should be limited
     * @param string|array $printerSet (Optional) 'set' string or array of printer ids to which the response should be limited
     * @return \PrintNode\Entity\PrintJob[]
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

        return $this->makeRequestMapped($url, 'GET', '\PrintNode\Entity\PrintJob');

    }

    /**
     * Makes a printjobState request to the PrintNode API, returning an array
     * of all the printjobs that have been processed on the active account.
     *
     * If a 'set' string or array of print job ids is passed to the third
     * argument, the returned array will be filtered to just those print jobs.
     *
     * Returned is an array of states in an array keyed by the print job id.
     *
     * @param int $offset (Optional) The start index for the records the API should return
     * @param int $limit (Optional) The number of records the API should return
     * @param string|array $printJobSet (Optional) 'set' string or array of print job ids to which the response should be limited
     * @return array
     */
    public function viewPrintJobState($offset = 0, $limit = 500, $printJobSet = null)
    {

        $url = 'printjobs/states';

        if (isset($printJobSet)) {
            $url = sprintf('printjobs/%s/states', $this->setImplode($printJobSet));
        }

        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);

        $this->lastResponse = $this->makeRequest($url, 'GET');

        if (!is_array($this->lastResponse->bodyJson)
            || (sizeof($this->lastResponse->bodyJson) == 0)) {
            return true;
        }

        $states = array();

        foreach ($this->lastResponse->bodyJson as $stateJson) {

            $jobState = $this->mapBodyJsonToEntityArray($stateJson, '\PrintNode\Entity\PrintJobState');

            $states[$jobState[0]->printJobId] = $jobState;

        }

        return $states;

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
     * @param string $computerId The id of the computer on which to view scales
     * @param string $deviceName (Optional) The name of the scale device
     * @param string $deviceNumber (Optional) The id of the scale device
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

        return $this->makeRequestMapped($url, 'GET', '\PrintNode\Entity\Scale', 'deviceNum');

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
     * @param int $offset (Optional) The start index for the records the API should return
     * @param int $limit (Optional) The number of records the API should return
     * @param string|array (Optional) $downloadSet 'set' string or array of download ids to which the response should be limited
     * @return array
     */
    public function viewClientDownloads($offset = 0, $limit = 1, $downloadSet = null)
    {

        $url = 'download/clients';

        if (isset($downloadSet)) {
            $url = sprintf('download/clients/%s', $this->setImplode($downloadSet));
        }

        $url = $this->applyLimitOffsetToUrl($url, $offset, $limit);

        return $this->makeRequestMapped($url, 'GET', '\PrintNode\Entity\ClientDownload');

    }

    /**
     * Appends the offset and limit arguments to a given api endpoint url
     *
     * @param string $url (Optional) The url to which any limits or offsets will be applied
     * @param int $offset (Optional) The offset to apply to the url
     * @param int $limit (Optional) The limit to apply to the url
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
     * Creates a new printJob by making a POST to the PrintNode API, returning
     * the new print job id, or optionally a printjob object is argument 2 is
     * populated
     *
     * @param \PrintNode\Entity\PrintJob $printJob A populated printjob object
     * @param bool $returnObject (Optional) If set to true, returns the full printjob data by making a second request
     * @return |PrintNode\Entity\PrintJob|string
     */
    public function createPrintJob($printJob, $returnObject = false)
    {

        $this->lastResponse = $this->makeRequest('printjobs', 'POST', \json_encode($printJob));

        if ($returnObject) {
            return $this->viewPrintJobs(0, 1, $this->lastResponse->body);
        }

        return $this->lastResponse->body;

    }

    /**
     * Creates a new child account by making a POST to the PrintNode API,
     * returning a ChildAccount object.
     *
     * @param \PrintNode\Entity\Account $account A populated account object
     * @param type $apiKeys (Optional) An array of API keys that should be created on the new account
     * @param type $tags (Optional) An array of Tags that should be created on the new account
     * @return \PrintNode\Entity\ChildAccount
     */
    public function createChildAccount(\PrintNode\Entity\Account $account, $apiKeys = null, $tags = null)
    {

        $newChildAccount = new \PrintNode\Entity\ChildAccount($this);

        $newChildAccount->Account = $account;

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

        $this->lastResponse = $this->makeRequest('account', 'POST', \json_encode($newChildAccount));

        $newChildAccount->Account->mapValuesFromJson($this->lastResponse->bodyJson->Account);

        $newChildAccount->ApiKeys = array();
        $newChildAccount->Tags = array();

        if ($this->lastResponse->bodyJson->ApiKeys instanceof \stdClass) {
            foreach ($this->lastResponse->bodyJson->ApiKeys as $apiDescription => $apiKey) {
                $newChildAccount->ApiKeys[$apiDescription] = $apiKey;
            }
        }

        if ($this->lastResponse->bodyJson->Tags instanceof \stdClass) {
            foreach ($this->lastResponse->bodyJson->Tags as $tagDescription => $tag) {
                $newChildAccount->Tags[$tagDescription] = $tag;
            }
        }

        return $newChildAccount;

    }

    /**
     * Modifies the currently active child account by making a PATCH request to
     * the PrintNode API, returning a Whoami object with the modified
     * account details
     *
     * @param \PrintNode\Entity\Account $account An account object containing the fields to be modified
     * @return \PrintNode\Entity\Whoami
     */
    public function modifyAccount($account)
    {

        $this->lastResponse = $this->makeRequest('account', 'PATCH', \json_encode($account));

        $whoAmI = new \PrintNode\Entity\Whoami($this);

        $whoAmI->mapValuesFromJson($this->lastResponse->bodyJson);

        return $whoAmI;

    }

    /**
     * Makes a delete account request to the PrintNode API for the active
     * child account.
     *
     * @return bool
     */
    public function deleteChildAccount()
    {

        $this->lastResponse = $this->makeRequest('account', 'DELETE');

        return $this->lastResponse->bodyJson;

    }

    /**
     * Makes a create tag request to the PrintNode API, creating the tag
     * specified in argument 1 with the value specified in argument 2 on the
     * active account.
     *
     * @param string $tagName The name of the tag to be created
     * @param string $tagValue The value of the tag to be created
     * @return string
     */
    public function createTag($tagName, $tagValue)
    {

        $this->lastResponse = $this->makeRequest('account/tag/' . $tagName, 'POST', \json_encode($tagValue));

        return $this->lastResponse->bodyJson;

    }

    /**
     * Makes a create tag request to the PrintNode API, returning the tag
     * specified in the first argument on the active account.
     *
     * @param string $tagName The name of the tag to view
     * @return string
     */
    public function viewTag($tagName)
    {

        $this->lastResponse = $this->makeRequest('account/tag/' . $tagName, 'GET');

        return $this->lastResponse->bodyJson;

    }

    /**
     * Makes a delete tag request to the PrintNode API, removing the tag
     * specified in the first argument on the active account.
     *
     * @param string $tagName The name of the tag to be deleted
     * @return string
     */
    public function deleteTag($tagName)
    {

        $this->lastResponse = $this->makeRequest('account/tag/' . $tagName, 'DELETE');

        return $this->lastResponse->bodyJson;

    }

    /**
     * Makes a view apikey request to the PrintNode API, returning the api key
     * string.
     *
     * @param string $apiKeyName The label of the API Key to be created
     * @return bool
     */
    public function createApiKey($apiKeyLabel)
    {

        $url = sprintf('/account/apikey/%s', $apiKeyLabel);

        $this->lastResponse = $this->makeRequest($url, 'POST');

        return $this->lastResponse->bodyJson;

    }

    /**
     * Makes a view apikey request to the PrintNode API, returning the api key
     * string.
     *
     * @param string $apiKeyName The label of the API Key to be returned
     * @return string
     */
    public function viewApiKey($apiKeyLabel)
    {

        $url = sprintf('/account/apikey/%s', $apiKeyLabel);

        $this->lastResponse = $this->makeRequest($url, 'GET');

        return $this->lastResponse->bodyJson;

    }

    /**
     * Makes a delete apikey request to the PrintNode API, returning true
     * if the key was successfully deleted.
     *
     * @param string $apiKeyName The label of the API Key to be deleted
     * @return bool
     */
    public function deleteApiKey($apiKeyLabel)
    {

        $url = sprintf('/account/apikey/%s', $apiKeyLabel);

        $this->lastResponse = $this->makeRequest($url, 'DELETE');

        return (bool)$this->lastResponse;

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
    public function makeRequest($url, $method, $content = null)
    {

        $this->lastRequest = new Request($this->credentials, $url, $method);

        $this->lastRequest->setPropertiesFromClient($this);

        if ($content !== null) {
            $this->lastRequest->body = $content;
        }

        return $this->lastRequest->process();

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
    public function makeRequestMapped($url, $method, $responseEntity = null, $keyname = 'id')
    {

        $this->lastResponse = $this->makeRequest($url, $method);

        if (!is_array($this->lastResponse->bodyJson)
            || (sizeof($this->lastResponse->bodyJson) == 0)) {
            return;
        }

        if ($responseEntity === null ) {

            return $this->lastResponse->bodyJson;

        } else {

            return $this->mapBodyJsonToEntityArray($this->lastResponse->bodyJson, $responseEntity, $keyname);

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
