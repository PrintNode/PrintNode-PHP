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
     * Map entity names to API URLs
     * @var string[]
     */
    private $endPointUrls = array(
        'Computer' => 'https://api.printnode.com/computers',
        'Printer' => 'https://api.printnode.com/printers',
        'PrintJob' => 'https://api.printnode.com/printjobs',
    );

    /**
     * Map method names used by __call to entity names
     * @var string[]
     */
    private $methodNameEntityMap = array(
        'Computers' => 'Computer',
        'Printers' => 'Printer',
        'PrintJobs' => 'PrintJob',
    );

    /**
     * Get API EndPoint URL from an entity name
     * @param mixed $entityName
     * @return string
     */
    private function getEndPointUrl($entityName)
    {
        if (!isset($this->endPointUrls[$entityName])) {

            throw new InvalidArgumentException(
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

            throw new BadMethodCallException(
                sprintf(
                    'Method %s::%s does not exist',
                    get_class($this),
                    $methodName
                )
            );
        }

        if (!isset($this->methodNameEntityMap[$matchesArray[1]])) {

            throw new BadMethodCallException(
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
        curl_setopt($curlHandle, CURLOPT_VERBOSE, true);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);

        curl_setopt($curlHandle, CURLOPT_USERPWD, (string)$this->credentials);

        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);

        return $curlHandle;
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

            throw new RuntimeException(
                sprintf(
                    'cURL Error (%d): %s',
                    curl_errno($curlHandle),
                    curl_error($curlHandle)
                )
            );
        }

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
        return $this->curlExec(
            $this->curlInit(),
            $this->applyOffsetLimit($endPointUrl)
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
        $queryStringArray['limit'] = min(max(1, $this->limit), 10);

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
    private function curlSend(Entity $entity, $httpMethod)
    {
        $curlHandle = $this->curlInit();

        $endPointUrl = $this->getEndPointUrl(get_class($entity));

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, (string)$entity);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

        return $this->curlExec(
            $curlHandle,
            $endPointUrl
        );
    }

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
            throw new RuntimeException('Function curl_init does not exist.');
        }

        $this->credentials = $credentials;

        if (count($endPointUrls)) {
            $this->endPointUrls = $endPointUrls;
        }

        if (count($methodNameEntityMap)) {
            $this->methodNameEntityMap = $methodNameEntityMap;
        }

        $this->setOffset($offset);
        $this->setLimit($limit);
    }

    /**
     * Set the offset for GET requests
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        if (!ctype_digit($offset) && !is_int($offset)) {
            throw new InvalidArgumentException('offset should be a number');
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
            throw new InvalidArgumentException('limit should be a number');
        }

        $this->limit = $limit;
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

            if (!ctype_digit($arguments) && !is_int($arguments)) {

                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid argument type passed to %s. Expecting a number got %s',
                        $methodName,
                        gettype($arguments)
                    )
                );
            }

            $endPointUrl = sprintf(
                '%s/%d',
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

            throw new RuntimeException(
                sprintf(
                    'HTTP Error (%d): %s',
                    $response->getStatusCode(),
                    $response->getStatusMessage()
                )
            );
        }

        return Entity::makeFromResponse($entityName, $response);
    }

    /**
     * POST (create) the specified entity
     * @param Entity $entity
     * @return Response
     */
    public function post(Entity $entity)
    {
        return $this->curlSend($entity, 'POST');
    }

    /**
     * PUT (update) the specified entity
     * @param Entity $entity
     * @return Response
     */
    public function put(Entity $entity)
    {
        return $this->curlSend($entity, 'PUT');
    }

    /**
     * DELETE (delete) the specified entity
     * @param Entity $entity
     * @return Response
     */
    public function delete(Entity $entity)
    {
        return $this->curlSend($entity, 'DELETE');
    }
}