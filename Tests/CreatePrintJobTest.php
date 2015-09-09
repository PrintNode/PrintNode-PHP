<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class CreatePrintJobTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @test
     */
    public function testCreatePrintJob()
    {
        
        $computerIds = array_values(printnodeSetUp());
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $printers = $client->viewPrinters(0, 500, null, $computerIds[0]);
        
        $printer = array_pop($printers);
        
        $printJob = new \PrintNode\Entity\PrintJob($client);
        
        $this->assertInstanceOf('\PrintNode\Entity\PrintJob', $printJob);

        $printJob->printer = $printer->id;
        $printJob->title = 'Test Printjob ' . rand(1,999999);
        $printJob->contentType = 'pdf_base64';
        $printJob->addPdfFile('Tests/assets/a4_portrait.pdf');
        $printJob->source = 'PrintNode PHP Unit Test';
        
        $printJobResult = $client->createPrintJob($printJob);
        
        $this->assertTrue((strlen($printJobResult) > 0));
        
    }
    
    /**
     * Tear down the remote environment if a previous method failed before
     * tearing down
     */
    public static function tearDownAfterClass()
    {
        
        printnodeTearDown();
        
    }
    
}