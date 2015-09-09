<?php

/**
 * This example makes a whoAmI request to the PrintNode API to retrieve the
 * details of the currently active account.
 */

include '../src/PrintNode/Bootstrap.php';

$credentials = new \PrintNode\Credentials\ApiKey('YOUR_API_KEY');

$client = new \PrintNode\Client($credentials);

$whoAmI = $client->viewWhoAmi();

echo $whoAmI;