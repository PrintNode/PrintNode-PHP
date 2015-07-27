<?php

include 'vendor/autoload.php';
$credentials = new PrintNode\Credentials();

/**
 * There are two ways of authenticating when manipulating a Child Account
 * 
 * - Using the Parent Account API Key
 * - Using the Child Account API Key
 * 
 * You can only manipulate a Child Account details when using the Parent Account API Key. 
 * For example: to change a Child Accounts name, email address, password or delete a Child Account 
 * you must be use the Parent Account API Key.
 * 
 * For this example, you must be authenticated as the Parent Account.
 **/

$credentials->setApiKey(PRINTNODE_APIKEY);
$request = new PrintNode\Request($credentials);

/**
 * You can specify the Child Account to manipulate using any of these methods:
 *  
 * - PrintNode\Request->setChildAccountById($id)
 * - PrintNode\Request->setChildAccountByEmail($email)
 * - PrintNode\Request->setChildAccountByCreatorRef($creatorRef)
 *
 * We will set the Child Account by ID
 **/
$request->setChildAccountById($id);

// All requests from this request object will now operate on this Child Account.

$whoami = $request->getWhoami();
$computers = $request->getComputers();
$printers = $request->getPrinters();

/**
 * We have authenticated using the Parent Account API Key and can therefore 
 * edit Child Acccount details:
 **/
$account = new PrintNode\Account();

// Let's change the Child Account name.
$account->Account = array(
    "firstname" => "ANewFirstName",
    "lastname" => "ANewLastName"
);

$request->patch($account);

$whoamiNow = $request->getWhoami();

// Let's delete our Child Account.
$request->deleteAccount();

?>
