<?php
include 'vendor/autoload.php';

$credentials = new PrintNode\Credentials();
$credentials->setApiKey(PRINTNODE_APIKEY);

$request = new PrintNode\Request($credentials);

// Initialise a Child Account
$account = new PrintNode\Account();

// Set properties on the Child Account
$account->Account = array(
    "firstname" => "A",
    "lastname" => "ALastName",
    "password" => "superStrongPassword",
    "email" => "email@example.com"
);

// Post the Child Account to the API
$aNewAccount = $request->post($account);

// You can get the Child Account ID from the response object
$id = $aNewAccount->GetDecodedContent()["Account"]["id"];
