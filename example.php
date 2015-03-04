<?php

// Include the PrintNode API. You may also use require here.
include 'Loader.php';

// Register the PrintNode autoloader.
PrintNode\Loader::init();

// Rest of my code below here
$credentials = new PrintNode\Credentials(
    'myApiKey.123',
    '123a456b7cd89efab89cd0efa12bcd3e45fab678'
);

$request = new PrintNode\Request($credentials);

$printers = $request->getPrinters();

$printJob = new PrintNode\Entities\PrintJob();
$printJob->printer = $printers[0]->id;
$printJob->contentType = 'pdf_uri';
$printJob->content = 'https://app.printnode.com/testpdfs/a4_portrait.pdf';
$printJob->source = 'My App';
$printJob->title = 'Test PrintJob from My App';

$response = $request->post($printJob);

var_dump($response);