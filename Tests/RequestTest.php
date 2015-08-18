<?php

use PrintNode\Request;
use PrintNode\Account;

use PrintNode\Entity\Computer;
use PrintNode\Entity\Printer;
use PrintNode\Entity\PrintJob;
use PrintNode\Entity\PrintJobState;
use PrintNode\Entity\Scale;
use PrintNode\Entity\Whoami;


class RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PrintNode/Credentials
     */
    protected $credentials;

    /**
     * @var array Computers
     */
    protected $computers = [];

    public function __construct()
    {
        $this->credentials = new PrintNode\Credentials\ApiKey(
            API_KEY,
            [],
            ['X-No-Throttle' => 'true']
        );
    }

    public function getRandomComputerId ()
    {
        $computerIds = array_values($this->computers);
        shuffle($computerIds);
        return $computerIds[0];
    }

    public function getRandomPrinterId ()
    {
        $request = new Request($this->credentials);
        $printers = $request->getPrinters();
        $printerIds = array_map(
            function ($printer) {
                return $printer->id;
            },
            $printers
        );
        shuffle($printerIds);
        return $printerIds[0];
    }

    public function createAccount()
    {
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

    public function makePrintJob()
    {
        $pdfFile = file_get_contents(__DIR__.'/assets/a4_portrait.pdf');
        $printJob = new PrintJob();
        $printJob->printer = $this->getRandomPrinterId();
        $printJob->contentType = 'pdf_base64';
        $printJob->content = base64_encode($pdfFile);
        $printJob->source = 'testing print';
        $printJob->title = 'Test Printjob for PHP API Tests';
        return $printJob;
    }

    protected function setup()
    {
        $ch = curl_init("https://apidev.printnode.com/test/data/generate");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->credentials->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $computers = json_decode($response, true);
        $this->computers = $computers;
        curl_close($ch);
    }

    protected function tearDown()
    {
        $ch = curl_init("https://apidev.printnode.com/test/data/generate");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->credentials->getHeaders());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }

    public function testWhoami ()
    {
        $request = new Request($this->credentials);
        $whoami = $request->getWhoami();
        $this->assertInstanceOf(get_class(new Whoami()), $whoami);
    }

    public function testComputers ()
    {
        $request = new Request($this->credentials);
        $computers = $request->getComputers();
        $this->assertInstanceOf(
            get_class(new Computer()),
            $computers[0]
        );
    }

    public function testComputersById ()
    {
        $computerId = $this->getRandomComputerId();
        $request = new Request($this->credentials);
        $computers = $request->getComputers($computerId);
        $this->assertSame(count($computers), 1);
        $this->assertSame($computers[0]->id, $computerId);
    }

    public function testPrinters ()
    {
        $request = new Request($this->credentials);
        $printers = $request->getPrinters();
        $this->assertInstanceOf(
            get_class(new Printer()),
            $printers[0]
        );
    }

    public function testPrintersByComputer ()
    {
        $request = new Request($this->credentials);
        $allComputers = $request->getComputers();
        $computerId = $allComputers[0]->id;

        $printers = $request->getPrinters($computerId);
        foreach ($printers as $printer) {
        	$this->assertSame($computerId, $printer->computer->id);
        }
    }

    public function testPrintersById ()
    {
        $printerId = $this->getRandomPrinterId();

        $request = new Request($this->credentials);
        $printers = $request->getPrinters(null, $printerId);

        foreach ($printers as $printer) {
            $this->assertSame($printerId, $printer->id);
        }
    }

    public function testPrintJobs ()
    {
        $request = new Request($this->credentials);
        $printjobs = $request->getPrintJobs();
        $this->assertInstanceOf(
            get_class(new PrintJob()),
            $printjobs[1]
        );
    }

    public function testPrintJobsById ()
    {
        $printerId = $this->getRandomPrinterId();

        $request = new Request($this->credentials);
        $printjobs = $request->getPrintJobs($printerId);
        foreach ($printjobs as $printjob) {
            $this->assertSame($printerId, $printjob->printer->id);
        }

        $printJobId = $printjob->id;
        $printjobs = $request->getPrintJobs(null, $printJobId);
        $this->assertSame(1, count($printjobs));
        $this->assertSame($printJobId, $printjobs[0]->id);
    }

    public function testPrintJobStates ()
    {
        $request = new Request($this->credentials);
        $printJobStates = $request->getPrintJobStates();
        $this->assertInstanceOf(
            get_class(new PrintJobState()),
            $printJobStates[0][0]
        );

        $printJobId = $printJobStates[1][0]->printJobId;
        $printJobStates = $request->getPrintJobStates($printJobId);
        $this->assertSame(1, count($printJobStates));
        foreach ($printJobStates[0] as $state) {
            $this->assertSame($printJobId, $state->printJobId);
        }
    }

    public function testGetScales()
    {
        $request = new Request($this->credentials);
        $scales = $request->getScales(0);
        $this->assertInstanceOf(
            get_class(new Scale()),
            $scales[0]
        );
    }

    public function testGetScalesByBadScaleName()
    {
        $request = new Request($this->credentials);
        $scales = $request->getScales(0, 'Not a real scale name');
        $this->assertSame(0, count($scales));
    }

    public function testGetScalesByRealScaleName()
    {
        $request = new Request($this->credentials);
        $scales = $request->getScales(0, 'PrintNode Test Scale');
        $this->assertSame(1, count($scales));
    }

    public function testGetScalesByRealScaleNameAndNum()
    {
        $request = new Request($this->credentials);
        $scales = $request->getScales(0, 'PrintNode Test Scale', 0);
        $this->assertInstanceOf(
            get_class(new Scale()),
            $scales
        );
    }

    /**
      * @expectedException  PrintNode\HTTPException
     **/
    public function testGetScalesByBadScaleNameAndNum()
    {
        $request = new Request($this->credentials);
        $scales = $request->getScales(0, 'PrintNode Test Scale', 'WTF');
    }

    public function testPostPrintJob ()
    {
        $request = new Request($this->credentials);
        $printJob = $this->makePrintJob();
        $response = $request->post($printJob);
        $printJobId = $response->getDecodedContent();
        $this->assertInternalType('int', $printJobId);
    }

}
