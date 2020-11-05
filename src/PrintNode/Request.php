<?php

namespace PrintNode;

/**
 * Request
 *
 * HTTP request object.
 *
 * @method Computer[] getComputers() getComputers(int $computerId)
 * @method Printer[] getPrinters() getPrinters(int $printerId)
 * @method PrintJob[] getPrintJobs() getPrintJobs(int $printJobId)
 */
class Request
{
    /**
     * Credentials to use when communicating with API
     * @var Credentials
     */
    private $credentials;
    /**
     * API url to use with the client
     * @var string
     * */
    private $apiurl = "https://api.printnode.com";
    /**
     * Header for child authentication
     * @var string[]
     * */
    private $childauth = array();
    /**
     * Offset query argument on GET requests
     * @var int
     */
    private $offset = 0;

    /**
     * Limit query argument on GET requests
     * @var mixed
     */
    private $limit = 10;

    /**
    * Curl timeout in seconds.
    * @var int
    */
    private $curltimeout = 30;

    /**
     * Custom headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * Map entity names to API URLs
     * @var string[]
     */
    private $endPointUrls = array(
        'PrintNode\Client' => '/download/clients',
        'PrintNode\Download' => '/download/client',
        'PrintNode\ApiKey' => '/account/apikey',
        'PrintNode\Account' => '/account',
        'PrintNode\Tag' => '/account/tag',
        'PrintNode\Whoami' => '/whoami',
        'PrintNode\Computer' => '/computers',
        'PrintNode\Printer' => '/printers',
        'PrintNode\PrintJob' => '/printjobs',
    );

    /**
     * Map method names used by __call to entity names
     * @var string[]
     */
    private $methodNameEntityMap = array(
        'Clients' => 'PrintNode\Client',
        'Downloads' => 'PrintNode\Download',
        'ApiKeys' => 'PrintNode\ApiKey',
        'Account' => 'PrintNode\Account',
        'Tags' => 'PrintNode\Tag',
        'Whoami' => 'PrintNode\Whoami',
        'Computers' => 'PrintNode\Computer',
        'Printers' => 'PrintNode\Printer',
        'PrintJobs' => 'PrintNode\PrintJob',
    );

    /**
     * Constructor
     * @param Credentials $credentials
     * @param mixed $endPointUrls
     * @param mixed $methodNameEntityMap
     * @param int $offset
     * @param int $limit
     * @return Request
     */
    public function __construct(Credentials $credentials, array $endPointUrls = array(), array $methodNameEntityMap = array(), $offset = 0, $limit = 10)
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('Function curl_init does not exist.');
        }

        $this->credentials = $credentials;

        if ($endPointUrls) {
            $this->endPointUrls = $endPointUrls;
        }

        if ($methodNameEntityMap) {
            $this->methodNameEntityMap = $methodNameEntityMap;
        }

        $this->makeEndPointUrls();

        $this->setOffset($offset);
        $this->setLimit($limit);
    }


    /**
     * Get API EndPoint URL from an entity name
     * @param mixed $entityName
     * @return string
     */
    private function makeEndPointUrls()
    {
        $endPointUrls = array();
        foreach ($this->methodNameEntityMap as $classes) {
            $endPointUrls[$classes] = $this->apiurl.$this->endPointUrls[$classes];
        }
        $this->endPointUrls = $endPointUrls;
    }

    private function getEndPointUrl($entityName)
    {
        if (!isset($this->endPointUrls[$entityName])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Missing endPointUrl for entityName "%s"',
                    $entityName
                )
            );
        }

        return $this->endPointUrls[$entityName];
    }

    /**
     * Get entity name from __call method name
     * @param mixed $methodName
     * @return string
     */
    private function getEntityName($methodName)
    {
        if (!preg_match('/^get(.+)$/', $methodName, $matchesArray)) {
            throw new \BadMethodCallException(
                sprintf(
                    'Method %s::%s does not exist',
                    get_class($this),
                    $methodName
                )
            );
        }

        if (!isset($this->methodNameEntityMap[$matchesArray[1]])) {
            throw new \BadMethodCallException(
                sprintf(
                    '%s is missing an methodNameMap entry for %s',
                    get_class($this),
                    $methodName
                )
            );
        }

        return $this->methodNameEntityMap[$matchesArray[1]];
    }

    /**
     * Initialise cURL with the options we need
     * to communicate successfully with API URL.
     * @param void
     * @return resource
     */
    private function curlInit()
    {
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_ENCODING, 'gzip,deflate');

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);

        curl_setopt($curlHandle, CURLOPT_USERPWD, (string)$this->credentials);

        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);

		curl_setopt($curlHandle, CURLOPT_TIMEOUT, (int)$this->curltimeout);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);

        return $curlHandle;
    }

    public function setCurlTimeout($timeout)
    {
        if (!ctype_digit($timeout) && !is_int($timeout)) {
            throw new \InvalidArgumentException('timeout should be a number');
        }
        $this->curltimeout = $timeout;
    }

    /**
     * Execute cURL request using the specified API EndPoint
     * @param mixed $curlHandle
     * @param mixed $endPointUrl
     * @return Response
     */
    private function curlExec($curlHandle, $endPointUrl)
    {
        curl_setopt($curlHandle, CURLOPT_URL, $endPointUrl);

        if (($response = @curl_exec($curlHandle)) === false) {
            throw new \RuntimeException(
                sprintf(
                    'cURL Error (%d): %s',
                    curl_errno($curlHandle),
                    curl_error($curlHandle)
                )
            );
        }

		curl_close($curlHandle);

        $response_parts = explode("\r\n\r\n", $response);

        $content = array_pop($response_parts);

        $headers = explode("\r\n", array_pop($response_parts));

        return new Response($endPointUrl, $content, $headers);
    }

    /**
     * Make a GET request using cURL
     * @param mixed $endPointUrl
     * @return Response
     */
    private function curlGet($endPointUrl)
    {
        $curlHandle = $this->curlInit();
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->childauth);

        $endPointUrl = $this->applyOffsetLimit($endPointUrl);

        return $this->curlExec(
            $curlHandle,
            $endPointUrl
        );
    }

    private function curlDelete($endPointUrl)
    {
        $curlHandle = $this->curlInit();

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->childauth);

        return $this->curlExec(
            $curlHandle,
            $endPointUrl
        );
    }

    /**
     * Apply offset and limit to a end point URL.
     * @param mixed $endPointUrl
     * @return string
     */
    private function applyOffsetLimit($endPointUrl)
    {
        $endPointUrlArray = parse_url($endPointUrl);

        if (!isset($endPointUrlArray['query'])) {
            $endPointUrlArray['query'] = null;
        }

        parse_str($endPointUrlArray['query'], $queryStringArray);

        $queryStringArray['offset'] = $this->offset;
        $queryStringArray['limit'] = min(max(1, $this->limit), 500);

        $endPointUrlArray['query'] = http_build_query($queryStringArray, null, '&');

        $endPointUrl = (isset($endPointUrlArray['scheme'])) ? "{$endPointUrlArray['scheme']}://" : '';
        $endPointUrl.= (isset($endPointUrlArray['host'])) ? "{$endPointUrlArray['host']}" : '';
        $endPointUrl.= (isset($endPointUrlArray['port'])) ? ":{$endPointUrlArray['port']}" : '';
        $endPointUrl.= (isset($endPointUrlArray['path'])) ? "{$endPointUrlArray['path']}" : '';
        $endPointUrl.= (isset($endPointUrlArray['query'])) ? "?{$endPointUrlArray['query']}" : '';

        return $endPointUrl;
    }

    /**
     * Make a POST/PUT/DELETE request using cURL
     * @param Entity $entity
     * @param mixed $httpMethod
     * @return Response
     */
    private function curlSend()
    {
        $arguments = func_get_args();

        $httpMethod = array_shift($arguments);
        $data = array_shift($arguments);
        $endPointUrl = array_shift($arguments);

        $curlHandle = $this->curlInit();

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, (string)$data);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array_merge(
            array('Content-Type: application/json'),
            $this->childauth,
            $this->headers)
        );

        return $this->curlExec(
            $curlHandle,
            $endPointUrl
        );
    }
    /**
     * Set the offset for GET requests
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        if (!ctype_digit($offset) && !is_int($offset)) {
            throw new \InvalidArgumentException('offset should be a number');
        }

        $this->offset = $offset;
    }

    /**
     * Set the limit for GET requests
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        if (!ctype_digit($limit) && !is_int($limit)) {
            throw new \InvalidArgumentException('limit should be a number');
        }

        $this->limit = $limit;
    }

    /**
     * Delete an ApiKey for a child account
     * @param string $apikey
     * @return Response
     * */
    public function deleteApiKey($apikey)
    {
        $endPointUrl = $this->apiurl."/account/apikey/".$apikey;

        $response = $this->curlDelete($endPointUrl);

        return $response;
    }

    /**
     * Delete a tag for a child account
     * @param string $tag
     * @return Response
     * */
    public function deleteTag($tag)
    {
        $endPointUrl = $this->apiurl."/account/tag/".$tag;

        $response = $this->curlDelete($endPointUrl);

        return $response;
    }

    /**
     * Delete a child account
     * MUST have $this->childauth set to run.
     * @return Response
     * */
    public function deleteAccount()
    {
        if (!isset($this->childauth)) {
            throw new Exception(
                sprintf(
                    'No child authentication set - cannot delete your own account.'
                )
            );
        }

        $endPointUrl = $this->apiurl."/account/";

        $response = $this->curlDelete($endPointUrl);

        return $response;
    }

    /**
     * Returns a client key.
     * @param string $uuid
     * @param string $edition
     * @param string $version
     * @return Resposne
     * */
    public function getClientKey($uuid, $edition, $version)
    {
        $endPointUrl = $this->apiurl."/client/key/".$uuid."?edition=".$edition."&version=".$version;

        $response = $this->curlGet($endPointUrl);

        return $response;
    }

    /**
     * Gets print job states.
     * @param string $printjobId OPTIONAL:if unset gives states relative to all printjobs.
     * @return Entity[]
     * */
    public function getPrintJobStates()
    {
        $arguments = func_get_args();

        if (count($arguments) > 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Too many arguments given to getPrintJobsStates.'
                )
            );
        }

        $endPointUrl = $this->apiurl."/printjobs/";

        if (count($arguments) == 0) {
            $endPointUrl.= 'states/';
        } else {
            $arg_1 = array_shift($arguments);
            $endPointUrl.= $arg_1.'/states/';
        }

        $response = $this->curlGet($endPointUrl);

        if ($response->getStatusCode() != '200') {
            throw new \RuntimeException(
                sprintf(
                    'HTTP Error (%d): %s',
                    $response->getStatusCode(),
                    $response->getStatusMessage()
                )
            );
        }

        return Entity::makeFromResponse("PrintNode\State", json_decode($response->getContent()));
    }


    /**
     * Gets PrintJobs relative to a printer.
     * @param string $printerIdSet set of printer ids to find PrintJobs relative to
     * @param string $printJobId OPTIONAL: set of PrintJob ids relative to the printer.
     * @return Entity[]
     * */
    public function getPrintJobsByPrinters()
    {
        $arguments = func_get_args();

        if (count($arguments) > 2) {
            throw new InvalidArgumentException(
                sprintf(
                    'Too many arguments given to getPrintJobsByPrinters.'
                )
            );
        }

        $endPointUrl = $this->apiurl."/printers/";

        $arg_1 = array_shift($arguments);

        $endPointUrl.= $arg_1.'/printjobs/';

        foreach ($arguments as $argument) {
            $endPointUrl.= $argument;
        }

        $response = $this->curlGet($endPointUrl);

        if ($response->getStatusCode() != '200') {
            throw new \RuntimeException(
                sprintf(
                    'HTTP Error (%d): %s',
                    $response->getStatusCode(),
                    $response->getStatusMessage()
                )
            );
        }

        return Entity::makeFromResponse("PrintNode\PrintJob", json_decode($response->getContent()));
    }

    /**
     * Gets scales relative to a computer.
     * @param string $computerId id of computer to find scales
     * @return Entity[]
     * */
    public function getScales($computerId)
    {
        $endPointUrl = $this->apiurl."/computer/";
        $endPointUrl.= $computerId;
        $endPointUrl.= '/scales';

        $response = $this->curlGet($endPointUrl);


        if ($response->getStatusCode() != '200') {
            throw new \RuntimeException(
                sprintf(
                    'HTTP Error (%d): %s',
                    $response->getStatusCode(),
                    $response->getStatusMessage()
                )
            );
        }

        return Entity::makeFromResponse("PrintNode\Scale", json_decode($response->getContent()));
    }

    /**
     * Get printers relative to a computer.
     * @param string $computerIdSet set of computer ids to find printers relative to
     * @param string $printerIdSet OPTIONAL: set of printer ids only found in the set of computers.
     * @return Entity[]
     * */
    public function getPrintersByComputers()
    {
        $arguments = func_get_args();

        if (count($arguments) > 2) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Too many arguments given to getPrintersByComputers.'
                )
            );
        }

        $endPointUrl = $this->apiurl."/computers/";

        $arg_1 = array_shift($arguments);

        $endPointUrl.= $arg_1.'/printers/';

        foreach ($arguments as $argument) {
            $endPointUrl.= $argument;
        }

        $response = $this->curlGet($endPointUrl);

        if ($response->getStatusCode() != '200') {
            throw new \RuntimeException(
                sprintf(
                    'HTTP Error (%d): %s',
                    $response->getStatusCode(),
                    $response->getStatusMessage()
                )
            );
        }

        return Entity::makeFromResponse("PrintNode\Printer", json_decode($response->getContent()));
    }

    /**
     * Map method names getComputers, getPrinters and getPrintJobs to entities
     * @param mixed $methodName
     * @param mixed $arguments
     * @return Entity[]
     */
    public function __call($methodName, $arguments)
    {
        $entityName = $this->getEntityName($methodName);

        $endPointUrl = $this->getEndPointUrl($entityName);

        if (count($arguments) > 0) {
            $arguments = array_shift($arguments);

            if (!is_string($arguments)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid argument type passed to %s. Expecting a string got %s',
                        $methodName,
                        gettype($arguments)
                    )
                );
            }

            $endPointUrl = sprintf(
                '%s/%s',
                $endPointUrl,
                $arguments
            );
        } else {
            $endPointUrl = sprintf(
                '%s',
                $endPointUrl
            );
        }

        $response = $this->curlGet($endPointUrl);

        if ($response->getStatusCode() != '200') {
            throw new \RuntimeException(
                sprintf(
                    'HTTP Error (%d): %s',
                    $response->getStatusCode(),
                    $response->getStatusMessage()
                )
            );
        }

        return Entity::makeFromResponse($entityName, json_decode($response->getContent()));
    }

    /**
     * PATCH (update) the specified entity
     * @param Entity $entity
     * @return Response
     * */
    public function patch(Entity $entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid argument type passed to patch. Expecting Entity got %s',
                    gettype($entity)
                )
            );
        }

        $endPointUrl = $this->getEndPointUrl(get_class($entity));

        if (method_exists($entity, 'endPointUrlArg')) {
            $endPointUrl.= '/'.$entity->endPointUrlArg();
        }

        if (method_exists($entity, 'formatForPatch')) {
            $entity = $entity->formatForPatch();
        }


        return $this->curlSend('PATCH', $entity, $endPointUrl);
    }

    /**
     * POST (create) the specified entity
     * @param Entity $entity
     * @return Response
     */
    public function post(Entity $entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid argument type passed to patch. Expecting Entity got %s',
                    gettype($entity)
                )
            );
        }

        $endPointUrl = $this->getEndPointUrl(get_class($entity));

        if (method_exists($entity, 'endPointUrlArg')) {
            $endPointUrl.= '/'.$entity->endPointUrlArg();
		}

		if (method_exists($entity, 'formatForPost')){
			$entity = $entity->formatForPost();
		}

        return $this->curlSend('POST', $entity, $endPointUrl);
    }

    /**
     * PUT (update) the specified entity
     * @param Entity $entity
     * @return Response
     */
    public function put()
    {
        $arguments = func_get_args();

        $entity = array_shift($arguments);

        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid argument type passed to patch. Expecting Entity got %s',
                    gettype($entity)
                )
            );
        }

        $endPointUrl = $this->getEndPointUrl(get_class($entity));

        foreach ($arguments as $argument) {
            $endPointUrl.= '/'.$argument;
        }

        return $this->curlSend('PUT', $entity, $endPointUrl);
    }

    /**
     * DELETE (delete) the specified entity
     * @param Entity $entity
     * @return Response
     */
    public function delete(Entity $entity)
    {
        $endPointUrl = $this->getEndPointUrl(get_class($entity));

        if (method_exists($entity, 'endPointUrlArg')) {
            $endPointUrl.= '/'.$entity->endPointUrlArg();
        }

        return $this->curlDelete($endPointUrl);
    }

    public function setChildAccountById($id)
    {
        $this->childauth = array("X-Child-Account-By-Id: ".$id);
    }

    public function setChildAccountByEmail($email)
    {
        $this->childauth = array("X-Child-Account-By-Email: ".$email);
    }

    public function setChildAccountByCreatorRef($creatorRef)
    {
        $this->childauth = array("X-Child-Account-By-CreatorRef: ".$creatorRef);
    }

    public function setHeaders($headers){
        $this->headers = $headers;
    }
}
