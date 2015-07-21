<?php
include 'vendor/autoload.php';
$credentials = new PrintNode\Credentials();

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
