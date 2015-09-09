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
 * This example will cover retriving information about the computers and 
 * printers that are active on your PrintNode account.
 * /
 
/*
 * Firstly, we'll use the client to retrieve a list of the computers that are
 * active on your PrintNode account by calling the viewComputers() method. This 
 * method will make a GET request to the /computers service on the 
 * PrintNode API server.
 * 
 * The method will return an associative array of Computer objects, keyed by the
 * id of the Computer.
 */

$computers = $client->viewComputers();

/**
 * Let's iterate over the computer array and echo out the information from each 
 * computer.
 */

if (is_array($computers)) {
    foreach ($computers as $computerId => $computer) {
        echo $computer;
    }
}

/*
 * The output of the above should look something like this:
 * 
 * Array
 * (
 *     [id] => 83421
 *     [name] => MY-MACBOOK-PRO
 *     [inet] => 192.168.1.10
 *     [hostname] => charlie@MY-MACBOOK-PRO.local
 *     [version] => 4.7.3+31a
 *     [createTimestamp] => 2015-08-18T15:46:22.848Z
 *     [state] => connected
 * )
 * 
 * Array
 * (
 *     [id] => 84121
 *     [name] => MYWINDOWSPC
 *     [inet] => 192.168.1.20
 *     [hostname] => charlie@MYWINDOWSPC.local
 *     [version] => 4.7.3+31a
 *     [createTimestamp] => 2015-08-19T15:37:42.585Z
 *     [state] => connected
 * )
 * 
 */

/*
 * Now we'll retrieve a list of the printers that are active on your PrintNode 
 * account by calling the viewPrinters() method. This method will make a GET 
 * request to the /printers service on the PrintNode API server.
 * 
 * The method will return an associative array of Printer objects, keyed by the
 * id of the Printer.
 */

$printers = $client->viewPrinters();

/*
 * Let's iterate over the printer array and echo out the information from each 
 * printer.
 */

if (is_array($printers)) {
    foreach ($printers as $printerId => $printer) {
        echo $printer;
    }
}

/*
 * The output of the above should look something like this:
 * 
 * (The )
 * 
 * Array
 * (
 *     [id] => 74253
 *     [computer] => Array
 *         (
 *             [id] => 83421
 *             [name] => MY-MACBOOK-PRO
 *             [inet] => 192.168.1.10
 *             [hostname] => charlie@MY-MACBOOK-PRO.local
 *             [version] => 4.7.3+31a
 *             [createTimestamp] => 2015-08-18T15:46:22.848Z
 *             [state] => connected
 *         )
 * 
 *     [name] => OKI_C822_16DB6E
 *     [description] => OKI_C822_16DB6E
 *     [capabilities] => Array
 *       (
 *         // TRUNCATED
 *       )
 *     [default] => 
 *     [createTimestamp] => 2015-08-18T15:46:22.976Z
 *     [state] => online
 * )
 * 
 * Array
 * (
 *     [id] => 74854
 *     [computer] => Array
 *         (
 *             [id] => 84121
 *             [name] => MYWINDOWSPC
 *             [inet] => 192.168.1.20
 *             [hostname] => charlie@MYWINDOWSPC.local
 *             [version] => 4.7.3+31a
 *             [createTimestamp] => 2015-08-19T15:37:42.585Z
 *             [state] => connected
 *         )
 * 
 *     [name] => DYMO_LabelWriter_450_Turbo
 *     [description] => DYMO_LabelWriter_450_Turbo
 *     [capabilities] => Array
 *       (
 *         // TRUNCATED
 *       )
 *     [default] => 
 *     [createTimestamp] => 2015-08-18T15:46:22.976Z
 *     [state] => online
 * )
 * 
 */
