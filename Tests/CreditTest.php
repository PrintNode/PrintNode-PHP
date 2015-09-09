<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class CreditTest extends PHPUnit_Framework_TestCase
{

    /**
     * Tests the retrieval of a credit balance
     * @test
     */
    public function testViewCredits()
    {
        
        printnodeSetUp();
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $credits = $client->viewCredits();
        
        $this->assertTrue(\is_numeric($credits));
        
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