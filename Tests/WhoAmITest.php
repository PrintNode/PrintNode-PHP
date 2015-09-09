<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class WhoAmITest extends PHPUnit_Framework_TestCase
{

    /**
     * Tests a WhoAmI call, ensuring that the object returned has the correct
     * values available
     * 
     * @test
     */
    public function testViewWhoAmI()
    {
        
        $computers = \printnodeSetUp();
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $whoami = $client->viewWhoAmI();
        
        $this->assertInstanceOf('\PrintNode\Request', $client->lastRequest);
        $this->assertInstanceOf('\PrintNode\Response', $client->lastResponse);
        
        $this->assertInstanceOf('\PrintNode\Entity\WhoAmI', $whoami);
        
        $this->assertObjectHasAttribute('id', $whoami);
        $this->assertObjectHasAttribute('firstname', $whoami);
        $this->assertObjectHasAttribute('lastname', $whoami);
        $this->assertObjectHasAttribute('email', $whoami);
        $this->assertObjectHasAttribute('canCreateSubAccounts', $whoami);
        $this->assertObjectHasAttribute('creatorEmail', $whoami);
        $this->assertObjectHasAttribute('creatorRef', $whoami);
        $this->assertObjectHasAttribute('childAccounts', $whoami);
        $this->assertObjectHasAttribute('credits', $whoami);
        $this->assertObjectHasAttribute('numComputers', $whoami);
        $this->assertObjectHasAttribute('totalPrints', $whoami);
        $this->assertObjectHasAttribute('versions', $whoami);
        $this->assertObjectHasAttribute('connected', $whoami);
        $this->assertObjectHasAttribute('ApiKeys', $whoami);
        $this->assertObjectHasAttribute('state', $whoami);
        $this->assertObjectHasAttribute('permissions', $whoami);

        $this->assertEquals('Mr', $whoami->firstname);
        $this->assertEquals('Krishna', $whoami->lastname);
        $this->assertEquals('god@printnode.com', $whoami->email);
        $this->assertEquals(1, $whoami->canCreateSubAccounts);
        $this->assertEquals('', $whoami->creatorEmail);
        $this->assertEquals('', $whoami->creatorRef);

        $this->assertTrue(\is_numeric($whoami->credits));
        $this->assertEquals(sizeof($computers), $whoami->numComputers);
        $this->assertEquals(125, $whoami->totalPrints);

        \printnodeTearDown();
        
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