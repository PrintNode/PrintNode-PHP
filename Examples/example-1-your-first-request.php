<?php

/**
 * Firstly, we'll include the PrintNode bootstrap file. This registers the 
 * PrintNode autoloader for you.
 * 
 * If you've moved these example files, you may need to update this path.
 */
include '../src/PrintNode/Bootstrap.php';

/*
 * Let's create a PrintNode Credentials object. This will contain the 
 * information we'll use to authenticate ourselves with the PrintNode API.
 * 
 * You'll need to replace YOUR_API_KEY with your own API key.
 * 
 * You can find your API key (or generate a new one) by logging into the 
 * PrintNode dashboard at https://app.printnode.com and clicking the 
 * 'API' tab at the top.
 */

$credentials = new \PrintNode\Credentials\ApiKey('YOUR_API_KEY');

/*
 * Now we create an instance of the PrintNode Client, passing the Credentials 
 * object we created above in the first argument. The Client object is the 
 * manager for making requests to printNode.
 */

$client = new \PrintNode\Client($credentials);

/*
 * We can now use the client to retrieve our account information by calling the
 * viewWhoAmI() method, which makes a GET request to the /whoami service on 
 * the PrintNode API server.
 * 
 * The viewWhoAmI() method returns a WhoAmI object.
 * 
 * Client methods are always consistant, so methods that retrieve data always
 * begin with 'view', methods that create things with 'create' and methods that 
 * delete things with 'delete'.
 * 
 */

$whoAmI = $client->viewWhoAmi();

/*
 * You can now view the contents of the returned object by echoing it. All the
 * entities returned by this API client implement the toString() method for 
 * easy debugging.
 * 
 * Example WhoAmI data:
 * 
 *  Array
 *  (
 *      [id] => 61124
 *      [firstname] => Charlie
 *      [lastname] => Bloggs
 *      [email] => no-reply@printnode.com
 *      [canCreateSubAccounts] => 1
 *      [childAccounts] => Array
 *          (
 *              [0] => 61389
 *              [1] => 71334
 *          )
 *  
 *      [credits] => 10000
 *      [numComputers] => 2
 *      [totalPrints] => 49
 *      [versions] => Array
 *          (
 *          )
 *  
 *      [connected] => Array
 *          (
 *          )
 *  
 *      [Tags] => Array
 *          (
 *          )
 *  
 *      [state] => active
 *      [permissions] => Array
 *          (
 *              [0] => Unrestricted
 *          )
 *  
 *  )
 * 
 */

echo $whoAmI;

/**
 * All the properties you see above can also be accessed directly
 * 
 */

echo $whoAmI->firstname . ' ' . $whoAmI->lastname;