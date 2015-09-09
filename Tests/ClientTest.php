<?php

class ClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * Tests the correct creation of a client object,
     * and that all attributes and methods are present.
     * @test
     */
    public function testClient()
    {
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $this->assertInstanceOf('\PrintNode\Client', $client);
        
        $this->assertObjectHasAttribute('credentials', $client);
        $this->assertObjectHasAttribute('prettyJSON', $client);
        $this->assertObjectHasAttribute('dontLog', $client);
        $this->assertObjectHasAttribute('apiHost', $client);
        $this->assertObjectHasAttribute('lastResponse', $client);
        
        $this->assertTrue(\method_exists($client, 'viewWhoAmI'));
        $this->assertTrue(\method_exists($client, 'viewCredits'));
        $this->assertTrue(\method_exists($client, 'viewComputers'));
        $this->assertTrue(\method_exists($client, 'viewPrinters'));
        $this->assertTrue(\method_exists($client, 'viewPrintJobs'));
        $this->assertTrue(\method_exists($client, 'viewPrintJobState'));
        $this->assertTrue(\method_exists($client, 'viewScales'));
        $this->assertTrue(\method_exists($client, 'viewClientDownloads'));
        $this->assertTrue(\method_exists($client, 'applyLimitOffsetToUrl'));
        $this->assertTrue(\method_exists($client, 'createPrintJob'));
        $this->assertTrue(\method_exists($client, 'createChildAccount'));
        $this->assertTrue(\method_exists($client, 'modifyAccount'));
        $this->assertTrue(\method_exists($client, 'deleteChildAccount'));
        $this->assertTrue(\method_exists($client, 'createTag'));
        $this->assertTrue(\method_exists($client, 'viewTag'));
        $this->assertTrue(\method_exists($client, 'deleteTag'));
        $this->assertTrue(\method_exists($client, 'createApiKey'));
        $this->assertTrue(\method_exists($client, 'viewApiKey'));
        $this->assertTrue(\method_exists($client, 'deleteApiKey'));
        $this->assertTrue(\method_exists($client, 'setImplode'));
        $this->assertTrue(\method_exists($client, 'makeRequest'));
        $this->assertTrue(\method_exists($client, 'makeRequestMapped'));
        $this->assertTrue(\method_exists($client, 'mapBodyJsonToEntityArray'));
        $this->assertTrue(\method_exists($client, 'mapJsonToEntity'));
        
    }
    
    /**
     * Tests that client throws the appropriate exception when not passed
     * a constructor argument
     * 
     * @expectedException \Exception
     * @test
     */
    public function testClientMissingArgumentException()
    {
        
        new \PrintNode\Client();
        
    }
    
    /**
     * Tests that client throws the appropriate exception when not passed
     * a valid constructor argument
     * 
     * @expectedException \Exception
     * @test
     */
    public function testClientIncorrectArgumentException()
    {
        
        new \PrintNode\Client('Invalid Argument');
        
    }
    
    /**
     * Tests that client throws the appropriate exception when not passed
     * a valid constructor argument
     * 
     * @expectedException \Exception
     * @test
     */
    public function testClientIncorrectObjectArgumentException()
    {
        
        new \PrintNode\Client(new \stdClass());
        
    }
    
}