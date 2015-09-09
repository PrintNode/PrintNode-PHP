<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class PrintJobsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testViewPrintJobs()
    {
    
        $computerIds = printnodeSetUp();
                
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $printJobs = $client->viewPrintJobs();
        
        $this->assertTrue(\is_array($printJobs));
        
        $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\PrintJob', $printJobs);
        
        $printJob = array_pop($printJobs);

        $this->assertObjectHasAttribute('id', $printJob);
        $this->assertObjectHasAttribute('printer', $printJob);
        $this->assertObjectHasAttribute('printerId', $printJob);
        $this->assertObjectHasAttribute('title', $printJob);
        $this->assertObjectHasAttribute('contentType', $printJob);
        $this->assertObjectHasAttribute('content', $printJob);
        $this->assertObjectHasAttribute('source', $printJob);
        $this->assertObjectHasAttribute('filesize', $printJob);
        $this->assertObjectHasAttribute('options', $printJob);
        $this->assertObjectHasAttribute('expireAfter', $printJob);
        $this->assertObjectHasAttribute('qty', $printJob);
        $this->assertObjectHasAttribute('authentication', $printJob);
        $this->assertObjectHasAttribute('createTimestamp', $printJob);
        $this->assertObjectHasAttribute('state', $printJob);
        $this->assertObjectHasAttribute('expireAt', $printJob);
        
        printnodeTearDown();
        
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