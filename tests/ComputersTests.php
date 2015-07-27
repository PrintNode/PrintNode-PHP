<?php

include 'credentials.php';
class RequestTest extends PHPUnit_Framework_TestCase
{

	protected $credentials;
	protected $apikey = API_KEY;

	public function createAccount(){
		$request = new PrintNode\Request($this->credentials);
		$account = new PrintNode\Account();
		$account->Account = array(
			"firstname" => "AFirstName",
			"lastname" => "ALastName",
			"email" => "email@emailprovider.com",
			"password" => "APassword"
		);
		$response = $request->post($account);
		return $response;
	}

	protected function setUp(){
		$this->credentials = new PrintNode\Credentials;
		$this->credentials->setApiKey($this->apikey);
		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch,CURLOPT_USERPWD,(string)$this->credentials);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_exec($ch);
		curl_close($ch);
	}

	protected function tearDown(){
		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch,CURLOPT_USERPWD,(string)$this->credentials);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_exec($ch);
		curl_close($ch);
		$this->credentials = null;
	}

	public function testApiKeyAuth(){
		$request = new PrintNode\Request($this->credentials);
		$this->assertInstanceOf('PrintNode\Whoami',$request->getWhoami());
	}
	/**
	* @depends testApiKeyAuth
	*
	* */
	public function testComputers(){
		$request = new PrintNode\Request($this->credentials);
		$this->assertInstanceOf('PrintNode\Computer',$request->getComputers()[0]);
	}

	/**
	* @depends testComputers
	*
	* */
	public function testPrinters(){
		$request = new PrintNode\Request($this->credentials);
		$printers = $request->getPrinters();
		$this->assertInstanceOf('PrintNode\Printer',$printers[0]);
	}

	/**
	* @depends testPrinters
	*
	* */
	public function testPrintJobs(){
		$request = new PrintNode\Request($this->credentials);
		$printjobs = $request->getPrintJobs();
		$this->assertInstanceOf('PrintNode\PrintJob',$printjobs[0]);
	}

	/**
	* @depends testPrintJobs
	*
	* */
	public function testPrintJobStates(){
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


}
