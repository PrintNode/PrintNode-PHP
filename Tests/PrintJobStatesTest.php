<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class PrintJobStatesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testViewPrintJobStates()
    {
        
        $computerIds = printnodeSetUp();
                
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $printJobs = $client->viewPrintJobs(0,5);
        
        $printJobIndexes = array_keys($printJobs);
        
        $printJobStatesArray = $client->viewPrintJobState(0, 500, $printJobIndexes);
        
        $this->assertTrue(\is_array($printJobs));
        
        foreach ($printJobStatesArray as $printJobId => $printJobStates) {
            
            $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\PrintJobState', $printJobStates);
        
            $printJobState = $printJobStates[0];
            
        }
        
        $this->assertObjectHasAttribute('printJobId', $printJobState);
        $this->assertObjectHasAttribute('state', $printJobState);
        $this->assertObjectHasAttribute('message', $printJobState);
        $this->assertObjectHasAttribute('data', $printJobState);
        $this->assertObjectHasAttribute('clientVersion', $printJobState);
        $this->assertObjectHasAttribute('createTimestamp', $printJobState);
        $this->assertObjectHasAttribute('age', $printJobState);

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