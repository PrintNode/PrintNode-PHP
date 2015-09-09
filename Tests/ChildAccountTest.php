<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class ChildAccountTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testCreateChildAccount()
    {
        
        $computerIds = array_values(printnodeSetUp());
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $newAccount = new \PrintNode\Entity\Account($client);

        $newAccount->firstname = 'Jamie';
        $newAccount->lastname = 'Printnode';
        $newAccount->email = 'pntest' . rand(11111,99999) . '@printnode.co.uk';
        $newAccount->password = 'password123';
        $newAccount->creatorRef = 'Test Child Account ' . rand(11111,99999);

        $apiKeys = array(
            'defaultapikey'
        );
        
        $tags = array(
            'createdBy' => 'PrintNode unit test',
        );
        
        $childAccount = $client->createChildAccount($newAccount, $apiKeys, $tags);
        
        $this->assertInstanceOf('\PrintNode\Entity\ChildAccount', $childAccount);
        
        $this->assertInstanceOf('\PrintNode\Entity\Account', $childAccount->Account);
        $this->assertTrue(is_array($childAccount->Tags));
        $this->assertTrue(is_array($childAccount->ApiKeys));
                
        printnodeTearDown();
        
    }
    
    /**
     * @test
     */
    public function testModifyChildAccount()
    {
        
        $computerIds = array_values(printnodeSetUp());
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $whoAmI = $client->viewWhoAmI();
        
        print_r($whoAmI);
        
        printnodeTearDown();
        
    }
    
    /**
     * @test
     */
    public function testDeleteChildAccount()
    {
        
        $computerIds = array_values(printnodeSetUp());
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
                
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