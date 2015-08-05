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
		$this->credentials = new ApiKeyCredentials(API_KEY);
	}
	public function createAccount ()
	{
		$response = $request->post($account);
		return $response;
	}

	protected function setUp ()
	{
		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->credentials->getHeaders());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_exec($ch);
		curl_close($ch);
		$request = new Request($this->credentials);
		$this->account = new Account();
		$this->account->Account = array(
			"firstname" => "AFirstName",
			"lastname" => "ALastName",
			"email" => "email@emailprovider.com",
			"password" => "APassword"
		);
	}

	protected function tearDown(){

		$credentials = new ApiKeyCredentials(
			API_KEY,
			['email' => "email@emailprovider.com"]
		);

		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $credentials->getHeaders());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_exec($ch);
		curl_close($ch);
		$request = new Request($credentials);
		$request->deleteAccount();
		$this->account = null;
		$this->credentials = null;

	}

	public function testTags(){
		$request = new Request($this->credentials);
		$tag = new Tag();
		$tag->name = "atag";
		$tag->value = "atagvalue";
		$response = $request->post($tag);
		$this->assertInternalType('string', $response->getDecodedContent());
		$gettag = $request->getTags("atag");
		$this->assertEquals("atagvalue",$gettag);
		$deletetag = $request->DeleteTag("atag");
		$this->assertEquals(true,$deletetag->getDecodedContent());
	}

	public function testLatestClient(){
		$request = new Request($this->credentials);
		$client = $request->getDownloads("osx");
		$this->assertInstanceOf(
			get_class(new Download()),
			$client
		);
	}

	public function testClients(){
		$request = new Request($this->credentials);
		$clients = $request->getClients("10-15");
		$this->assertInstanceOf(
			get_class(new Client()),
			$clients[0]
		);
		$clients[0]->enabled = false;
		$response = $request->patch($clients[0]);
		$this->assertEquals(array($clients[0]->id), $response->getDecodedContent());
	}

	public function testAccountCreationAndDeletion(){

		$request = new Request(new ApiKeyCredentials(API_KEY));
		$account = $request->post($this->account)->getDecodedContent();
		$this->assertEquals("AFirstName", $account["Account"]["firstname"]);

		$request = new Request(
			new ApiKeyCredentials(API_KEY, ["id" => $account["Account"]["id"]])
		);
		$response = $request->deleteAccount()->getDecodedContent();
		$this->assertTrue($response);
	}

	/**
	* @depends testAccountCreationAndDeletion
	* */
	public function testModifyAccount ()
	{

		$request = new Request(new ApiKeyCredentials(API_KEY));
		$accountInfo = $request->post($this->account)->getDecodedContent();

		$request = new Request(new ApiKeyCredentials(API_KEY, ["id" => $accountInfo["Account"]["id"]]));
		$account = new Account();
		$account->Account  = array(
			"firstname" => "ANewFirstName",
			"lastname" => "ALastName",
			"email" => "email@emailprovider.com",
			"password" => "APassword"
		);
		$response = $request->patch($account)->getDecodedContent();

		$this->assertEquals("ANewFirstName", $response["firstname"]);
		$request->deleteAccount();
	}

	/**
	* @depends testAccountCreationAndDeletion
	* */
	public function testApiKey ()
	{

		$request = new Request(new ApiKeyCredentials(API_KEY));
		$accountInfo = $request->post($this->account)->getDecodedContent();

		$request = new Request(new ApiKeyCredentials(API_KEY, ["id" => $accountInfo["Account"]["id"]]));
		$apiKey = new ApiKey();
		$apiKey->description = "testing";
		$response = $request->post($apiKey);
		$this->assertInternalType("string",$response->getDecodedContent());
		$getKey = $request->getApiKeys("testing");
		$this->assertEquals($response->getDecodedContent(),$getKey);
		$request->deleteAccount();
	}

	public function testClientKey(){
		$request = new Request($this->credentials);
		$response = $request->getClientKey('0a756864-602e-428f-a90b-842dee47f57e', '4.7.1', 'printnode');
		$this->assertInternaltype("string", $response->getDecodedContent());
	}

}
