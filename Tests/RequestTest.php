<?php

use PrintNode\Request;
use PrintNode\Account;

use PrintNode\Entity\Computer;
use PrintNode\Entity\Printer;
use PrintNode\Entity\PrintJob;
use PrintNode\Entity\PrintJobState;
use PrintNode\Entity\Whoami;

class RequestTest extends PHPUnit_Framework_TestCase
{

	protected $credentials;
	protected $apikey = API_KEY;

	public function createAccount() {

		$request = new Request($this->credentials);
		$account = new Account();
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
		$this->credentials = new PrintNode\ApiKeyCredentials($this->apikey);
		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch, CURLOPT_USERPWD, (string) $this->credentials);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}

	protected function tearDown(){
		$ch = curl_init("https://apidev.printnode.com/test/data/generate");
		curl_setopt($ch, CURLOPT_USERPWD, (string)$this->credentials);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_exec($ch);
		curl_close($ch);
		$this->credentials = null;
	}

	public function testApiKeyAuth(){
		$request = new Request($this->credentials);
		$this->assertInstanceOf(
			get_class(new Whoami()),
			$request->getWhoami()
		);
	}

	/**
	* @depends testApiKeyAuth
	*
	*/
	public function testComputers(){
		$request = new Request($this->credentials);
		$this->assertInstanceOf(
			get_class(new Computer()),
			$request->getComputers()[0]
		);
	}

	/**
	* @depends testComputers
	*/
	public function testPrinters(){
		$request = new Request($this->credentials);
		$printers = $request->getPrinters();
		$this->assertInstanceOf(
			get_class(new Printer()),
			$printers[0]
		);
	}

	/**
	* @depends testPrinters
	*/
	public function testPrintJobs(){
		$request = new Request($this->credentials);
		$printjobs = $request->getPrintJobs();
		$this->assertInstanceOf(
			get_class(new PrintJob()),
			$printjobs[1]
		);
	}

	/**
	* @depends testPrintJobs
	**/
	public function testPrintJobStates ()
	{
		$request = new Request($this->credentials);
		$printjobstates = $request->getPrintJobStates();
		$this->assertInstanceOf(
			get_class(new PrintJobState()),
			$printjobstates[0][0]
		);
	}

	/**
	* @depends testComputers
	* @depends testPrinters
	* @depends testPrintJobs
	**/
	public function testPrintingCombination(){
		$request = new Request($this->credentials);
		$computers = $request->getComputers();
		$PrintersByComputers = $request->getPrintersByComputers($computers[0]->id);
		$PrintJobsByPrinters = $request->getPrintJobsByPrinters($PrintersByComputers[0]->id);
		$this->assertEquals($PrintJobsByPrinters[0]->printer->id, $PrintersByComputers[0]->id);
		$this->assertEquals($PrintersByComputers[0]->computer->id,$computers[0]->id);
	}
#
#	/**
#	* @depends testPrintJobs
#	*
#	**/
#	public function testPrintJobsPost(){
#
#		$pdfFile = file_get_contents(__DIR__.'/assets/a4_portrait.pdf');
#		$request = new Request($this->credentials);
#		$printers = $request->getPrinters();
#		$printJob = new PrintNode\PrintJob();
#		$printJob->printer = $printers[0];
#		$printJob->contentType = 'pdf_base64';
#		$printJob->content = base64_encode($pdfFile);
#		$printJob->source = 'testing print';
#		$printJob->title = 'Test Printjob for PHP API Tests';
#		$response = $request->post($printJob);
#		$this->assertInternalType('int',$response->GetDecodedContent());
#	}

}
