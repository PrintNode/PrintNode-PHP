<?php

namespace PrintNode;

use PrintNode\Entity\Account;

if (!function_exists('curl_init')) {
    throw new \RuntimeException('Function curl_init() does not exist. Have you installed php curl?');
}

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
     * API url to use with the client
     * @var string
     * */
    private $apiHost;

    /**
     * Header for child authentication
     * @var string[]
     * */
    protected $headers = array();

    /**
     * Offset query argument on GET requests
     * @var int
     */
    protected $offset = 0;

    /**
     * Limit query argument on GET requests
     * @var mixed
     */
    protected $limit = 10;

    /**
     * Map entity names to API URLs
     * @var string[]
     */
    private $endPointUrls = array(
        'PrintNode\\Entity\\Account' => '/account',
        'PrintNode\\Entity\\Client' => '/download/clients',
        'PrintNode\\Entity\\Computer' => '/computers',
        'PrintNode\\Entity\\Download' => '/download/client',
        'PrintNode\\Entity\\Printer' => '/printers',
        'PrintNode\\Entity\\PrintJob' => '/printjobs',
        'PrintNode\\Entity\\PrintJobState' => '/printjob/states',
        'PrintNode\\Entity\\Whoami' => '/whoami',
    );

    /**
     * Map method names used by __call to entity names
     * @var string[]
     */
    private $methodNameEntityMap = array(
        'Accounts' => 'PrintNode\\Entity\\Account',
        'Clients' => 'PrintNode\\Entity\\Client',
        'Computers' => 'PrintNode\\Entity\\Computer',
        'Downloads' => 'PrintNode\\Entity\\Download',
        'Printers' => 'PrintNode\\Entity\\Printer',
        'PrintJobs' => 'PrintNode\\Entity\\PrintJob',
        'PrintJobStates' => 'PrintNode\\Entity\\PrintJobState',
        'Scales' => 'PrintNode\\Entity\\Scale',
        'Whoami' => 'PrintNode\\Entity\\Whoami',
    );

    /**
     * @var array Array of account properties
     */
    private $accountPropertiesAllowedUpdate = array(
        'firstname',
        'lastname',
        'password',
        'email',
        'creatorRef',
    );

    /**
     * Constructor
     *
     * @param Credentials $credentials
     * @param mixed $endPointUrls
     * @param mixed $methodNameEntityMap
     * @param int $offset
     * @param int $limit
     * @return Request
     */
    public function __construct(Credentials $credentials, $apiHost = "https://apidev.printnode.com", $endPointUrls = array(), array $methodNameEntityMap = array(), $offset = 0, $limit = 10)
    {

        $this->apiHost = $apiHost;

        if ($endPointUrls) {
            $this->endPointUrls = $endPointUrls;
        }
        if ($methodNameEntityMap) {
            $this->methodNameEntityMap = $methodNameEntityMap;
        }

        $this->setOffset($offset);
        $this->setLimit($limit);

		$this->headers = $credentials->getHeaders();
    }

    /**
     * Execute cURL request using the specified API EndPoint
     *
     * @param mixed $curlHandle
     * @param mixed $endPointUrl
     * @return Response
     */
    protected function curlExec ($method, $url, $body = null)
    {

        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 4);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlHandle, CURLOPT_URL, $url);

        # set http headers
        if (isset($body)) {
            $headers = array_merge(
                array('Content-Type: application/json'),
                $this->headers
            );
        } else {
            $headers = $this->headers;
        }
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);

        # post data?
        if ($body) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, (string) $body);
        }


        if (false === $response = @curl_exec($curlHandle)) {
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

        return new Response($method, $url, $content, $headers);
    }

    /**
     * Apply offset and limit to a end point URL.
     *
     * @param mixed $endPointUrl
     * @return string
     */
    protected function applyOffsetLimit ($url)
    {
        $urlArray = parse_url($url);

        if (!isset($urlArray['query'])) {
            $urlArray['query'] = null;
        }

        parse_str($urlArray['query'], $queryStringArray);

        $queryStringArray['offset'] = $this->offset;
        $queryStringArray['limit'] = min(max(1, $this->limit), 500);

        $urlArray['query'] = http_build_query($queryStringArray, null, '&');

        $url = (isset($urlArray['scheme'])) ? "{$urlArray['scheme']}://" : '';
        $url .= (isset($urlArray['host'])) ? "{$urlArray['host']}" : '';
        $url .= (isset($urlArray['port'])) ? ":{$urlArray['port']}" : '';
        $url .= (isset($urlArray['path'])) ? "{$urlArray['path']}" : '';
        $url .= (isset($urlArray['query'])) ? "?{$urlArray['query']}" : '';

        return $url;
    }

    /**
     * Given a Entity return a api endpoint for that entity
     *
     * @param String
     * @return String
     */
    private function getEndPointUrl ($entityName)
    {
        if (!isset($this->endPointUrls[$entityName])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Missing endPointUrl for entityName "%s"',
                    $entityName
                )
            );
        }
        return $this->apiHost.$this->endPointUrls[$entityName];
    }

    /**
     * Get entity name from __call method name
     *
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
     * Create or update tag
     *
     * @param string Name of tag
     * @param string Value of tag
     * @return string
     */
    public function createOrUpdateTag($name, $value)
    {
        if (!is_string($name)) {
            throw new RuntimeException("Tag name must be a string");
        }
        if (!is_string($value)) {
            throw new RuntimeException("Tag value must be a string");
        }
        if (strlen($name) > 64) {
            throw new RuntimeException("Tag name too long. Max length 64.");
        }
        if (strlen($value) > 1024) {
            throw new RuntimeException("Tag value too long. Max length 1024.");

        }
        $response = $this->curlExec(
            'POST',
            "{$this->apiHost}/account/tag/".rawurlencode($name),
            json_encode($value)
        );

        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent();
    }

    /**
     * Delete a tag
     *
     * @param string Tag name
     */
    public function deleteTag($name)
    {
        if (!is_string($name)) {
            throw new RuntimeException("Tag name must be a string");
        }
        if (strlen($name) > 64) {
            throw new RuntimeException("Tag name too long. Max length 64.");
        }

        $response = $this->curlExec(
            'DELETE',
            "{$this->apiHost}/account/tag/".rawurlencode($name)
        );


        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent();
    }

    /**
     * Get a tag
     *
     * @param string Tag name
     */
    public function getTag($name)
    {
        if (!is_string($name)) {
            throw new RuntimeException("Tag name must be a string");
        }
        if (strlen($name) > 64) {
            throw new RuntimeException("Tag name too long. Max length 64.");
        }

        $response = $this->curlExec(
            'GET',
            "{$this->apiHost}/account/tag/".rawurlencode($name)
        );

        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent();
    }

    /**
     * Create a ApiKey for a account
     *
     * @param string $apikey
     * @return Response
     **/
    public function createApiKey($description)
    {
        $endPointUrl = $this->apiHost."/account/apikey/".rawurlencode($description);
        $response = $this->curlExec('POST', $endPointUrl);
        return $response->getDecodedContent();
    }

    /**
     * Get a API Key for a account
     */
    public function getApiKey($description)
    {
        $endPointUrl = $this->apiHost."/account/apikey/".rawurlencode($description);
        $response = $this->curlExec('GET', $endPointUrl);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent();
    }

    /**
     * Delete an ApiKey for a account
     *
     * @param string $apikey
     **/
    public function deleteApiKey($apikey)
    {
        $endPointUrl = $this->apiHost."/account/apikey/".rawurlencode($apikey);
        $response = $this->curlExec('DELETE', $endPointUrl);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent();
    }

    /**
     * Whoami?
     */
    public function getWhoAmi()
    {
        $url = $this->apiHost."/whoami";
        $response = $this->curlExec('GET', $url);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse(
            $this->methodNameEntityMap['Whoami'],
            $response->getDecodedContent()
        );
    }

    /**
     * Create account
     *
     * @var PrintNode\Entity\Account Account or something which looks like PrintNode\Entity\Account
     */
    public function createAccount($account)
    {
        if ($account instanceof Account) {
            $body = $account->toArray();
        }
        $response = $this->curlExec('POST', $this->apiHost."/account", json_encode($body));
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent(true);
    }

    /**
     * Update a account's properties
     *
     * @param array Account properties in key value format
     */
    public function updateAccount(array $properties)
    {
        $update = array();
        foreach ($this->accountPropertiesAllowedUpdate as $property) {
            if (isset($properties[$property])) {
                $update[$property] = $properties[$property];
            }
        }
        $response = $this->curlExec(
            'PATCH',
            "{$this->apiHost}/account",
            json_encode($update)
        );
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent();
    }

    /**
     * Delete a account based on account credentials
     * @return Response
     * */
    public function deleteAccount()
    {
        $endPointUrl = $this->apiHost."/account/";
        return $this->curlExec('DELETE', $endPointUrl);
    }

    /**
     * Returns a client key.
     *
     * @param string $uuid
     * @param string $edition
     * @param string $version
     * @return Resposne
     * */
    public function getClientKey($uuid, $edition, $version)
    {
        $url = sprintf(
            '%s/client/key/%s?edition=%s&version=%s',
            $this->apiHost,
            rawurlencode($uuid),
            rawurlencode($edition),
            rawurlencode($version)
        );
        return $this->curlExec('GET', $url);
    }

    /**
     * Gets print job states.
     *
     * @param string $printjobId OPTIONAL:if unset gives states relative to all printjobs.
     * @return Entity[]
     * */
    public function getPrintJobStates($printJobIds = null)
    {

        $url = $this->apiHost."/printjobs";
        if ($printJobIds) {
            $url .= "/".$printJobIds;
        }
        $url .= '/states';
        $url = $this->applyOffsetLimit($url);

        $response = $this->curlExec('GET', $url);

        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse(
            $this->methodNameEntityMap['PrintJobStates'],
            $response->getDecodedContent()
        );
    }

    /**
     * Get Latest Downloads
     *
     * @param string Operating system
     */
    public function getLatestDownload($operatingSystem)
    {
        $url = sprintf(
            "%s/download/client/%s",
            $this->apiHost,
            rawurlencode($operatingSystem)
        );

        $response = $this->curlExec('GET', $url);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse(
            $this->methodNameEntityMap['Downloads'],
            $response->getDecodedContent()
        );
    }

    /**
     * Get Latest Downloads
     *
     * @param string Operating system
     */
    public function getClients($clientIds = null)
    {
        $url = "{$this->apiHost}/download/clients";
        if ($clientIds) {
            $url .= "/".rawurlencode($clientIds);
        }

        $response = $this->curlExec('GET', $url);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse(
            $this->methodNameEntityMap['Clients'],
            $response->getDecodedContent()
        );
    }

    /**
     * Enable/Client clients
     *
     * @param string|int $clientIds
     * @param bool Enabled state
     **/
    public function enabledClients($clientIds, $enabled)
    {
        $url = sprintf("%s/download/clients/%s", $this->apiHost, $clientIds);
        $body = array('enabled' => (bool) $enabled);
        $response = $this->curlExec('PATCH', $url, json_encode($body));

        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return $response->getDecodedContent();
    }


    /**
     * Gets PrintJobs relative to a printer.
     *
     * @param string $printerSet set of printer ids to find PrintJobs relative to
     * @param string $printJobId OPTIONAL: set of PrintJob ids relative to the printer.
     * @return Entity[]
     **/
    public function getPrintJobs($printerSet = null, $printJobSet = null)
    {

        $url = $this->apiHost;
        if ($printerSet) {
            $url .= "/printers/{$printerSet}";
        }
        $url .= '/printjobs';
        if ($printJobSet) {
            $url .= "/{$printJobSet}";
        }
        $url = $this->applyOffsetLimit($url);

        $response = $this->curlExec('GET', $url);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse($this->methodNameEntityMap['PrintJobs'], $response->getDecodedContent());
    }

    /**
     * Gets scales relative to a computer.
     *
     * @param string $computerId id of computer to find scales
     * @return Entity[]
     **/
    public function getScales($computerId, $scaleName = null, $scaleNum = null)
    {

        $url = sprintf(
            "%s/computer/%s/scale%s",
            $this->apiHost,
            $computerId,
            !isset($scaleNum) ? 's' : ''
        );

        if (isset($scaleName)) {
            $url .= "/".rawurlencode($scaleName);
        }
        if (isset($scaleNum)) {
            $url .= "/".rawurlencode($scaleNum);
        }

        $response = $this->curlExec('GET', $url);

        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse($this->methodNameEntityMap['Scales'], $response->getDecodedContent());

    }

    /**
     * Get printers relative to a computer.
     *
     * @param string $computerIdSet set of computer ids to find printers relative to
     * @param string $printerIdSet OPTIONAL: set of printer ids only found in the set of computers.
     * @return Entity[]
     **/
    public function getPrinters($computerSet = null, $printerSet = null)
    {

        $url = $this->apiHost;
        if ($computerSet) {
            $url .= "/computers/{$computerSet}";
        }
        $url .= '/printers';
        if ($printerSet) {
            $url .= "/{$printerSet}";
        }

        $url = $this->applyOffsetLimit($url);

        $response = $this->curlExec('GET', $url);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse($this->methodNameEntityMap['Printers'], $response->getDecodedContent());
    }

    /**
     * Get computers
     *
     * @param string $computerSet
     * @return Entity[]
     */
    public function getComputers($computerSet = null)
    {
        $url = "{$this->apiHost}/computers";
        if ($computerSet) {
            $url .= "/{$computerSet}";
        }
        $url = $this->applyOffsetLimit($url);

        $response = $this->curlExec('GET', $url);
        if (!$response->isOK()) {
            throw $response->getHTTPException();
        }
        return Entity::makeFromResponse($this->methodNameEntityMap['Computers'], $response->getDecodedContent());
    }

    /**
     * POST (create) the specified entity
     * @param Entity $entity
     * @return Response
     */
    public function post(Entity $entity)
    {
        if (!($entity instanceof Entity)) {
            throw new InvalidArgumentException(
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

        return $this->curlExec(
            'POST',
            $endPointUrl,
            $entity
        );
    }

}
