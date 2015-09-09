<?php

/*
 * We've already learnt about the Bootstrap file, the Credentials object and
 * the Client object in example 1.
 * 
 * As in example 2, this example assumes you have the PrintNode client running
 * on one or more computers with printers available.
 */

header("Content-Type:text/plain");

include '../src/PrintNode/Bootstrap.php';

$credentials = new \PrintNode\Credentials\ApiKey('YOUR_API_KEY');

$client = new \PrintNode\Client($credentials);

$printJobs = $client->viewPrintJobs(0, 2);

if (is_array($printJobs)) {
    foreach ($printJobs as $printJobId => $printJob) {
        echo $printJob;
    }
}