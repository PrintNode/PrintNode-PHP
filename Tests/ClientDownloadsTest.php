<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class ClientDownloadTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testClientDownloads()
    {
        
        $computerIds = array_values(printnodeSetUp());
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $clientDownloads = $client->viewClientDownloads();
        
        $this->assertTrue(\is_array($clientDownloads));
        
        $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\ClientDownload', $clientDownloads);
        
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