<?php

use PrintNode\Request;
use PrintNode\Entity\Account;
use PrintNode\Entity\ApiKey;
use PrintNode\Entity\Client;
use PrintNode\Entity\Download;
use PrintNode\Entity\Tag;

use PrintNode\Credentials\ApiKey as ApiKeyCredentials;

class AccountsTests extends PHPUnit_Framework_TestCase
{

    protected $credentials;
    protected $account;

    public function __construct()
    {
        parent::__construct();
        $this->credentials = new ApiKeyCredentials(API_KEY, [], ['X-No-Throttle' => 'true']);
    }

    private function getAccount()
    {
    	$entropy = uniqid();
        $account = new Account();
        $account->Account = array(
            "firstname" => "AFirstName",
            "lastname" => "ALastName",
            "email" => "email-{$entropy}@emailprovider.com",
            "password" => "APassword"
        );
        return $account;
    }

    protected function setUp ()
    {
        $ch = curl_init("https://apidev.printnode.com/test/data/generate");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->credentials->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }

    protected function tearDown ()
    {
        $ch = curl_init("https://apidev.printnode.com/test/data/generate");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->credentials->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_exec($ch);
        curl_close($ch);
    }

    public function testTags ()
    {
        $request = new Request($this->credentials);
        $response = $request->createOrUpdateTag("tag", "value");
        $this->assertInternalType('string', $response);
        $gettag = $request->getTag("tag");
        $this->assertEquals("value",$gettag);
        $deletetag = $request->deleteTag("tag");
        $this->assertEquals(true, $deletetag);
    }

    /**
      * @expectedException  PrintNode\HTTPException
     **/
    public function testDeleteNotExistingTag ()
    {
        $request = new Request($this->credentials);
        $request->deleteTag("tag");
    }

    public function testAccountCreationAndDeletion ()
    {
    	$reqAccount = $this->getAccount();

        $request = new Request(new ApiKeyCredentials(API_KEY, [], ['X-No-Throttle' => 'true']));
        $account = $request->createAccount($reqAccount);
        $this->assertEquals("AFirstName", $account["Account"]["firstname"]);

        $request = new Request(
            new ApiKeyCredentials(API_KEY, ["id" => $account["Account"]["id"]])
        );
        $response = $request->deleteAccount()->getDecodedContent();
        $this->assertTrue($response);
    }

    public function testModifyAccount ()
    {
    	$reqAccount = $this->getAccount();
        $request = new Request(new ApiKeyCredentials(API_KEY, [], ['X-No-Throttle' => 'true']));
        $accountInfo = $request->createAccount($reqAccount);
        $request = new Request(new ApiKeyCredentials(API_KEY, ["id" => $accountInfo["Account"]["id"]]));

        $accountProperties  = array(
            "firstname" => "ANewFirstName",
            "lastname" => "ANewLastName",
            "email" => "anewemail3@emailprovider.com",
            "password" => "ANewPassword"
        );
        $response = $request->updateAccount($accountProperties);

        $this->assertEquals($accountProperties['firstname'], $response->firstname);
        $this->assertEquals($accountProperties['lastname'], $response->lastname);
        $this->assertEquals($accountProperties['email'], $response->email);
        $request->deleteAccount();
    }

    public function testClientKey ()
    {
        $request = new Request($this->credentials);
        $response = $request->getClientKey('0a756864-602e-428f-a90b-842dee47f57e', '4.7.1', 'printnode');
        $this->assertInternaltype("string", $response->getDecodedContent());
    }


    public function testLatestClient ()
    {
        $request = new Request($this->credentials);
        $client = $request->getLatestDownload("osx");
        $this->assertInstanceOf(
            get_class(new Download()),
            $client
        );
    }

    public function testClients ()
    {
        $request = new Request($this->credentials);
        $clients = $request->getClients();
        $this->assertInstanceOf(
            get_class(new Client()),
            $clients[0]
        );

        $this->assertTrue(count($clients) > 1);
        $clients = $request->getClients($clients[0]->id);
        $this->assertTrue(count($clients) == 1);

        $client = $clients[0];

        $response = $request->enabledClients($client->id, !$client->enabled);
        $this->assertEquals(array($client->id), $response);
    }

    public function testApiKey ()
    {
    	$reqAccount = $this->getAccount();

        $request = new Request(new ApiKeyCredentials(API_KEY, [], ['X-No-Throttle' => 'true']));
        $accountInfo = $request->createAccount($reqAccount);

        $request = new Request(new ApiKeyCredentials(API_KEY, ["id" => $accountInfo["Account"]["id"]], ['X-No-Throttle' => 'true']));
        $response = $request->createApiKey("testing");
        $this->assertInternalType("string", $response);

        $getKey = $request->getApiKey("testing");
        $this->assertEquals($response, $getKey);

        $response = $request->deleteApiKey("testing");
        $this->assertTrue($response === true);

        $request->deleteAccount();
    }

}
