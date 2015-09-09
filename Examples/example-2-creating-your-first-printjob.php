<?php

/*
 * We've already learnt about the Bootstrap file, the Credentials object and
 * the Client object in example 1.
 */

include '../src/PrintNode/Bootstrap.php';

$credentials = new \PrintNode\Credentials\ApiKey('YOUR_API_KEY');

$client = new \PrintNode\Client($credentials);

/*
 * In this example, we're going to create a basic print job. In order to do 
 * this, we'll need to know the PrintNode ID of the printer we're going to 
 * send the print job to.
 * 
 * This example assumes that you have the PrintNode client installed on a 
 * computer with one or more printers installed on them and that you've
 * entered your username and password into the client.
 * 
 * If you haven't installed the client yet, you should do this now.
 * 
 * Once the client is installed, log into the PrintNode administration interface 
 * at https://app.printnode.com/account/ and click on the 'Printers' tab.
 * 
 * You should now see a list of all the printers installed on your computer. Find 
 * the printer that you'd like to send your first Print Job to and make a note
 * of the printer Id in the left-most column of the table.
 * 
 * If you don't see any Printers listed, you may have an issue with your 
 * PrintNode installation. Our troubleshooting guide is available at the 
 * following link:
 * 
 * https://www.printnode.com/docs/troubleshooting/
 * 
 */

/*
 * Firstly, we create a PrintNode print job object and set the job title
 * and source properties.
 */

$printJob = new \PrintNode\Entity\PrintJob($client);

$printJob->title = 'My Test Printjob';
$printJob->source = 'PrintNode Example 2 Script';

/**
 * Set the PrintNode printer id - make sure you replace YOUR_PRINTER_ID with the
 * printer id you made a note of above.
 */

$printJob->printer = 'YOUR_PRINTER_ID';

/**
 * Your test print job will contain a base 64 encoded PDF. The client will take 
 * care of encoding the PDF, you just need to give it the path to the file.
 */
$printJob->contentType = 'pdf_base64';
$printJob->addPdfFile('assets/a4_portrait.pdf');

/**
 * We can now use the client to create our new print job by passing the PrintJob
 * object to the createPrintJob method. This method will make a POST request
 * to the /printjob service on the PrintNode API server.
 * 
 * The method will return a string containing the id of the new PrintJob.
 */

$printJobId = $client->createPrintJob($printJob);

/**
 * Example Output:
 *  
 *  54981
 * 
 */

echo $printJobId;