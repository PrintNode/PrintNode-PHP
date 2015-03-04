<?php

// Include the PrintNode API. You may also use require here.
include 'Loader.php';

// Register the PrintNode autoloader.
PrintNode\PrintNode_Loader::init();

// Rest of my code below here
$credentials = new PrintNode\PrintNode_Credentials(
    'myApiKey.123',
    '123a456b7cd89efab89cd0efa12bcd3e45fab678'
);

$request = new PrintNode\PrintNode_Request($credentials);

$printers = $request->getPrinters();

var_dump($printers);