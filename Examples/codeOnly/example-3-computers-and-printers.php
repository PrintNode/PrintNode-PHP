<?php

/**
 * This example covers getting lists of computers and printers
 */

header("Content-Type:text/plain");

include '../src/PrintNode/Bootstrap.php';

$credentials = new \PrintNode\Credentials\ApiKey('YOUR_API_KEY');

$client = new \PrintNode\Client($credentials);

$computers = $client->viewComputers();

if (is_array($computers)) {
    foreach ($computers as $computerId => $computer) {
        echo $computer;
    }
}

$printers = $client->viewPrinters();

if (is_array($printers)) {
    foreach ($printers as $printerId => $printer) {
        echo $printer;
    }
}