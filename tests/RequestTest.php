<?php

include 'credentials.php';
class RequestTest extends PHPUnit_Framework_TestCase
{

	protected $credentials;
	protected $apikey = API_KEY;

	protected function setUp(){
		$this->credentials = new PrintNode\Credentials;
	}

	protected function tearDown(){
		$this->credentials = null;
	}

	public function testApiKeyAuth(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$this->assertInstanceOf('PrintNode\Whoami',$request->getWhoami());
	}
	/**
	* @depends testApiKeyAuth
	*
	* */
	public function testComputers(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$this->assertInstanceOf('PrintNode\Computer',$request->getComputers()[0]);
	}

	/**
	* @depends testComputers
	*
	* */
	public function testPrinters(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$printers = $request->getPrinters();
		$this->assertInstanceOf('PrintNode\Printer',$printers[0]);
	}

	/**
	* @depends testPrinters
	*
	* */
	public function testPrintJobs(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$printjobs = $request->getPrintJobs();
		$this->assertInstanceOf('PrintNode\PrintJob',$printjobs[0]);
	}

	/**
	* @depends testPrintJobs
	*
	* */
	public function testPrintJobStates(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$printjobstates = $request->getPrintJobStates();
		$this->assertInstanceOf('PrintNode\State',$printjobstates[0][0]);
	}

	/**
	* @depends testComputers
	* @depends testPrinters
	* @depends testPrintJobs
	* */
	public function testPrintingCombination(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$computers = $request->getComputers();
		$PrintersByComputers = $request->getPrintersByComputers($computers[0]->id);
		$PrintJobsByPrinters = $request->getPrintJobsByPrinters($PrintersByComputers[0]->id);
		$this->assertEquals($PrintJobsByPrinters[0]->printer->id, $PrintersByComputers[0]->id);
		$this->assertEquals($PrintersByComputers[0]->computer->id,$computers[0]->id);
	}

	/**
	* @depends testPrintJobs
	*
	* */
	public function testPrintJobsPost(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$printers = $request->getPrinters();
		$printJob = new PrintNode\PrintJob();
		$printJob->printer = $printers[0];
		$printJob->contentType = 'pdf_base64';
		$printJob->content = base64_encode(file_get_contents('a4_portrait.pdf'));
		$printJob->source = 'testing print';
		$printJob->title = 'Test Printjob for PHP API Tests';
		$response = $request->post($printJob);
		$this->assertInternalType('int',$response->GetDecodedContent());
	}
	/**
	* @depends testApiKeyAuth
	*
	* */
	public function testTags(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$tag = new PrintNode\Tag();
		$tag->name = "atag";
		$tag->value = "atagvalue";
		$response = $request->post($tag);
		$this->assertInternalType('string',$response->GetDecodedContent());
		$gettag = $request->getTags("atag");
		$this->assertEquals("atagvalue",$gettag);
		$deletetag = $request->DeleteTag("atag");
		$this->assertEquals(true,$deletetag->GetDecodedContent());
	}

	/**
	* @depends testApiKeyAuth
	*
	* */
	public function testLatestClient(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$client = $request->getDownloads("osx");
		$this->assertInstanceOf('PrintNode\Download',$client);
	}

	/**
	* @depends testApiKeyAuth
	*
	* */
	public function testClients(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$clients = $request->getClients("10-15");
		$this->assertInstanceOf('PrintNode\Client',$clients[0]);
		$clients[0]->enabled = false;
		$response = $request->patch($clients[0]);
		$this->assertEquals(array($clients[0]->id),$response->GetDecodedContent());
	}

	/**
	* @depends testApiKeyAuth
	*
	* */
	public function testAccountCreationAndDeletion(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$account = new PrintNode\Account();
		$account->Account = array(
			"firstname" => "AFirstName",
			"lastname" => "ALastName",
			"email" => "email@emailprovider.com",
			"password" => "APassword"
		);
		$response = $request->post($account);
		$this->assertEquals("AFirstName",$response->GetDecodedContent()["Account"]["firstname"]);
		$request->setChildAccountById($response->GetDecodedContent()["Account"]["id"]);
		$response = $request->DeleteAccount();
		$this->assertEquals(true,$response->GetDecodedContent());
	}

	/**
	* @depends testAccountCreationAndDeletion
	*
	* */
	public function testModifyAccount(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$account = new PrintNode\Account();
		$account->Account = array(
			"firstname" => "AFirstName",
			"lastname" => "ALastName",
			"email" => "email@emailprovider.com",
			"password" => "APassword"
		);
		$response = $request->post($account);
		$request->setChildAccountById($response->GetDecodedContent()["Account"]["id"]);
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
	*
	* */
	public function testApiKey(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$account = new PrintNode\Account();
		$account->Account = array(
			"firstname" => "AFirstName",
			"lastname" => "ALastName",
			"email" => "email@emailprovider.com",
			"password" => "APassword"
		);
		$response = $request->post($account);
		$request->setChildAccountById($response->GetDecodedContent()["Account"]["id"]);
		$ApiKey = new PrintNode\ApiKey();
		$ApiKey->description = "testing";
		$response = $request->post($ApiKey);
		$this->assertInternalType("string",$response->GetDecodedContent());
		$getKey = $request->getApiKeys("testing");
		$this->assertEquals($response->GetDecodedContent(),$getKey);
		$request->DeleteAccount();
	}

	/**
	* @depends testApiKeyAuth
	*
	* */
	public function testClientKey(){
		$this->credentials->setApiKey($this->apikey);
		$request = new PrintNode\Request($this->credentials);
		$response = $request->getClientKey('0a756864-602e-428f-a90b-842dee47f57e','4.7.1','printnode');
		$this->assertInternaltype("string",$response->GetDecodedContent());
	}

}
?>
