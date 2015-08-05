<?php

namespace PrintNode;

/**
 * Credentials
 *
 * @desc Used Request when communicating with API server.
 */
abstract class Credentials
{

    /**
     * Stores the headers which will be sent to the server
     * @var array
     */
    protected $headers = array();

    /**
     * Child account option keys to header names
     * @var array
     */
    private static $allowedChildAccountOptions = array(
        'id' => 'X-Child-Account-By-Id',
        'email' => 'X-Child-Account-By-Email',
        'creatorRef' => 'X-Child-Account-By-CreatorRef',
    );

    /**
     * Set a header for a request
     * @return PrintNode\Credentials
     */
	protected function setHeader($name, $value)
    {
        $this->headers[] = sprintf('%s: %s', $name, $value);
        return $this;
    }

    /**
     * Set a BasicAuthe style authorisation header
     */
    protected function setBasicAuthHeader ($username = '', $password = '')
    {
        $hash = base64_encode(sprintf('%s:%s', $username, $password));
        $this->setHeader('Authorization', "Basic {$hash}");
    }

    /**
     * Convert a passed childAccountOptionsHeader into HTTP Headers
     */
    protected function parseChildAccountOptions (array $childAccountOptions)
    {
        foreach ($childAccountOptions as $key => $value) {
            if (isset(self::$allowedChildAccountOptions[$key])) {
                $this->setHeader(self::$allowedChildAccountOptions[$key], $value);
            } else {
                $allowedValues = array_keys(self::$allowedChildAccountOptions);
                throw new \RunTimeException(
                    sprintf(
                        "Unknown child account option header. Allowed options are %s.",
                        implode(', ', $allowedValues)
                    )
                );
            }
        }
    }

    /**
     * Get a set of headers for the request
     */
    public function getHeaders ()
    {
        return $this->headers;
    }

}
