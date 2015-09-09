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

/*
 * This example will cover retriving information about print jobs and print
 * job statuses.
 *
 * Firstly we'll use the client to retrieve a list of the last two printjobs 
 * that were processed on your PrintNode account by calling the viewPrintJobs() 
 * method. This method will make a GET request to the /printjobs service on the 
 * PrintNode API server.
 * 
 * The method will return an associative array of PrintJob objects, keyed by the
 * id of the PrintJob.
 * 
 * We've set the first two arguments on the method in order to limit the amount
 * of records returned. The second argument is the number of records to retrieve
 * (10 in this case) and the first is the record to start on.
 * 
 */

$printJobs = $client->viewPrintJobs(0, 2);

/*
 * We'll now iterate over the PrintJobs array returned and echo out the
 * information on each print job
 */

if (is_array($printJobs)) {
    foreach ($printJobs as $printJobId => $printJob) {
        echo $printJob;
    }
}

/**
 * 
 * Array
 * (
 *     [id] => 4949091
 *     [printer] => Array
 *        (
 *          // TRUNCATED
 *        )
 *     [title] => A4 Portrait.pdf
 *     [contentType] => pdf_base64
 *     [source] => web interface
 *     [createTimestamp] => 2015-08-18T15:46:48.332Z
 *     [state] => done
 * )
 * Array
 * (
 *     [id] => 5024222
 *     [printer] => Array
 *        (
 *          // TRUNCATED
 *        )
 *     [title] => A4 Portrait.pdf
 *     [contentType] => pdf_base64
 *     [source] => web interface
 *     [createTimestamp] => 2015-08-20T10:56:42.300Z
 *     [state] => done
 * )
 * 
 */