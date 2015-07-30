<?php

use PrintNode\Request;
use PrintNode\ApiKeyCredentials;
use PrintNode\Entity\Account;
use PrintNode\Entity\ApiKey;
use PrintNode\Entity\Client;
use PrintNode\Entity\Download;
use PrintNode\Entity\Tag;

class AccountsTests extends PHPUnit_Framework_TestCase
{

	protected $credentials;
	protected $apikey = API_KEY;
	protected $account;

	public function createAccount ()
	{

		$response = $request->post($account);
		return $response;
	}

	protected function setUp ()
	{
		$this->credentials = new ApiKeyCredentials($this->apikey);
		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch, CURLOPT_USERPWD, (string) $this->credentials);
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
		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch,CURLOPT_USERPWD,(string)$this->credentials);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_exec($ch);
		curl_close($ch);
		$this->credentials->setChildAccountByEmail("email@emailprovider.com");
		$request = new Request($this->credentials);
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
		$this->assertInternalType('string',$response->GetDecodedContent());
		$gettag = $request->getTags("atag");
		$this->assertEquals("atagvalue",$gettag);
		$deletetag = $request->DeleteTag("atag");
		$this->assertEquals(true,$deletetag->GetDecodedContent());
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
		$this->assertEquals(array($clients[0]->id), $response->GetDecodedContent());
	}

	public function testAccountCreationAndDeletion(){
		$request = new Request($this->credentials);
		$response = $request->post($this->account);
		$this->assertEquals("AFirstName",$response->GetDecodedContent()["Account"]["firstname"]);
		$this->credentials->setChildAccountById($response->GetDecodedContent()["Account"]["id"]);
		$request = new Request($this->credentials);
		$response = $request->DeleteAccount();
		$this->assertEquals(true,$response->GetDecodedContent());
	}

	/**
	* @depends testAccountCreationAndDeletion
	* */
	public function testModifyAccount(){
		$request = new Request($this->credentials);
		$response = $request->post($this->account);
		$this->credentials->setChildAccountById($response->GetDecodedContent()["Account"]["id"]);
		$request = new Request($this->credentials);
		$account = new Account();
		$account->Account  = array(
			"firstname" => "ANewFirstName",
			"lastname" => "ALastName",
			"email" => "email@emailprovider.com",
			"password" => "APassword"
		);
		$response = $request->patch($account);
		$this->assertEquals("ANewFirstName",$response->GetDecodedContent()["firstname"]);
		$request->DeleteAccount();
	}

	/**
	* @depends testAccountCreationAndDeletion
	* */
	public function testApiKey(){
		$request = new Request($this->credentials);
		$response = $request->post($this->account);
		$this->credentials->setChildAccountById($response->GetDecodedContent()["Account"]["id"]);
		$request = new Request($this->credentials);
		$ApiKey = new ApiKey();
		$ApiKey->description = "testing";
		$response = $request->post($ApiKey);
		$this->assertInternalType("string",$response->GetDecodedContent());
		$getKey = $request->getApiKeys("testing");
		$this->assertEquals($response->GetDecodedContent(),$getKey);
		$request->DeleteAccount();
	}

	public function testClientKey(){
		$request = new Request($this->credentials);
		$response = $request->getClientKey('0a756864-602e-428f-a90b-842dee47f57e', '4.7.1', 'printnode');
		$this->assertInternaltype("string", $response->GetDecodedContent());
	}

}
