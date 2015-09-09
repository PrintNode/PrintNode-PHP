<?php

/**
 * This example covers creating a new PrintNode print job.
 */

include '../src/PrintNode/Bootstrap.php';

$credentials = new \PrintNode\Credentials\ApiKey('YOUR_API_KEY');

$client = new \PrintNode\Client($credentials);

$printJob = new \PrintNode\Entity\PrintJob($client);

$printJob->title = 'My Test Printjob';
$printJob->source = 'PrintNode Example 2 Script';
$printJob->printer = 'YOUR_PRINTER_ID';
$printJob->contentType = 'pdf_base64';
$printJob->addPdfFile('assets/a4_portrait.pdf');

$printJobId = $client->createPrintJob($printJob);

echo $printJobId;