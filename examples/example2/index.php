<?php
include 'vendor/autoload.php';

$credentials = new PrintNode\Credentials();
$credentials->setApiKey(PRINTNODE_APIKEY);


$request = new PrintNode\Request($credentials);
//This is a standard request as we have seen in the previous example. But what if we want to make a request with a child account?

//Firstly, we'll make a sample account so we don't remove any existing ones.
$account = new PrintNode\Account();

//We're going to post this object via request. Firstly, we need to fill the Account array.
$account->Account = array(
    "firstname" => "AFirstName",
    "lastname" => "ALastName",
    "password" => "APassword",
    "email" => "email@example.com"
);

$aNewAccount = $request->post($account);

//Let's get the id from the request.
$id = $aNewAccount->GetDecodedContent()["Account"]["id"];


//now, we make a new Request object (so we don't copy over the old one)
$request_child = new PrintNode\Request($credentials);

//Then we set our child credentials, by any of these methods:
//PrintNode\Request->setChildAccountById($id)
//PrintNode\Request->setChildAccountByEmail($email)
//PrintNode\Request->setChildAccountByCreatorRef($creatorRef)
$request_child->setChildAccountById($id);

//We can then do anything on that account.
$whoami = $request_child->getWhoami();
$computers = $request_child->getComputers();
$printers = $request_child->getPrinters();

//When authenticating like this, we have some specific things we can do only when we authenticate with a parent account's api-key:
$account = new PrintNode\Account();

$account->Account = array(
    "firstname" => "ANewFirstName",
    "lastname" => "ANewLastName"
);

$request_child->patch($account);

$whoamiNow = $request_child->getWhoami();

//And finally, we can delete our fake account.
$request_child->deleteAccount();
