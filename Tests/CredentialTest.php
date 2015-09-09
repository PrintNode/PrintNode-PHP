<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class CredentialTest extends PHPUnit_Framework_TestCase
{

    /**
     * Tests the correct creation of an api key credentials object,
     * and that all attributes and methods are present.
     * 
     * @test
     */
    public function testCredentialsApi()
    {
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);

        $this->assertInstanceOf('\PrintNode\Credentials', $credentials);
        $this->assertInstanceOf('\PrintNode\Credentials\ApiKey', $credentials);
        
        $this->assertObjectHasAttribute('apiKey', $credentials);
        $this->assertObjectHasAttribute('username', $credentials);
        $this->assertObjectHasAttribute('password', $credentials);
        
        $this->assertObjectHasAttribute('childAccountEmail', $credentials);
        $this->assertObjectHasAttribute('childAccountCreatorRef', $credentials);
        $this->assertObjectHasAttribute('childAccountId', $credentials);
        
        $this->assertTrue(\method_exists($credentials, 'setChildAccountByEmail'));
        $this->assertTrue(\method_exists($credentials, 'setChildAccountByCreatorRef'));
        $this->assertTrue(\method_exists($credentials, 'setChildAccountById'));
        $this->assertTrue(\method_exists($credentials, 'clearChildCredentials'));
                
    }

    /**
     * Tests the Credentials by ApiKey object throws the appropriate exception 
     * when not passed a constructor argument 
     * 
     * @expectedException \Exception
     * @test
     */
    public function testCredentialsApiMissingArgumentException()
    {
        
        new \PrintNode\Credentials\ApiKey();
        
    }
    
    /**
     * Tests the Credentials by ApiKey object throws the appropriate exception 
     * when and empty value is passed to the constructor argument 
     * 
     * @expectedException \PrintNode\Exception\InvalidArgumentException
     * @test
     */
    public function testCredentialsApiEmptyArgumentException()
    {
        
        new \PrintNode\Credentials\ApiKey('');
        
    }
    
    /**
     * Tests that client object throws the appropriate exception when something
     * that isn't a variable is passed to the argument
     * 
     * @expectedException \PrintNode\Exception\InvalidArgumentException
     * @test
     */
    public function testCredentialsApiInvalidArgumentException()
    {
        
        new \PrintNode\Credentials\ApiKey(new \stdClass());
        
    }
    
    /**
     * Tests that client object throws the appropriate exception when something
     * that isn't a variable is passed to the argument
     * 
     * @expectedException \PrintNode\Exception\HTTPException
     * @test
     */
    public function testCredentialsBadAPIKey()
    {
        
        \printnodeSetUp();
        
        $credentials = new \PrintNode\Credentials\ApiKey('AAAAAAAAAAAAAAAAAAAAA');
        
        $client = new \PrintNode\Client($credentials);
        
        $whoAmI = $client->viewWhoAmI();
        
        \printnodeTearDown();
        
    }
    
    /**
     * Tests the correct creation of a username credentials object
     * 
     * @test
     */
    public function testCredentialsUsername()
    {
        
        $credentials = new \PrintNode\Credentials\Username(API_USERNAME, API_PASSWORD);

        $this->assertInstanceOf('\PrintNode\Credentials', $credentials);
        $this->assertInstanceOf('\PrintNode\Credentials\Username', $credentials);
        
    }

    /**
     * Tests that the Username Credential throws the appropriate exception when 
     * the nothing is passed to the username argument
     * 
     * @expectedException \PrintNode\Exception\InvalidArgumentException
     * @test
     */
    public function testCredentialsUsernameMissingUsernameException()
    {
        
        $credentials = new \PrintNode\Credentials\Username(null, API_PASSWORD);
        
    }

    /**
     * Tests that the Username Credential throws the appropriate exception when 
     * an empty string is passed to the username argument
     * 
     * @expectedException \PrintNode\Exception\InvalidArgumentException
     * @test
     */
    public function testCredentialsUsernameEmptyUsernameException()
    {
        
        $credentials = new \PrintNode\Credentials\Username('', API_PASSWORD);
        
    }
    
    /**
     * Tests that the Username Credential throws the appropriate exception when 
     * an invalid value is passed to the username argument
     * 
     * @expectedException \PrintNode\Exception\InvalidArgumentException
     * @test
     */
    public function testCredentialsUsernameInvalidUsernameException()
    {
        
        $credentials = new \PrintNode\Credentials\Username(new stdClass(), API_PASSWORD);
        
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