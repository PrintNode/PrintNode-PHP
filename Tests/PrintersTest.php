<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class PrintersTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testViewPrinters()
    {
    
        $computerIds = printnodeSetUp();
                
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $printers = $client->viewPrinters();
        
        $this->assertTrue(\is_array($printers));
        
        $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\Printer', $printers);
        
        $printer = array_pop($printers);
        
        $this->assertObjectHasAttribute('id', $printer);
        $this->assertObjectHasAttribute('computer', $printer);
        $this->assertObjectHasAttribute('name', $printer);
        $this->assertObjectHasAttribute('description', $printer);
        $this->assertObjectHasAttribute('capabilities', $printer);
        $this->assertObjectHasAttribute('default', $printer);
        $this->assertObjectHasAttribute('createTimestamp', $printer);
        $this->assertObjectHasAttribute('state', $printer);
        
        printnodeTearDown();
        
    }
    
    /**
     * @test
     */
    public function testViewSinglePrinter()
    {
    
        $computerIds = printnodeSetUp();
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $limitedComputerIds = array_values($computerIds);
        
        $computerId = $limitedComputerIds[0];
        
        $expectedPrinterNames = array();
        
        for ($i=1; $i<=5; $i++) {
            $expectedPrinterNames[] = sprintf('%s.%s.TEST-PRINTER', $computerId, $i);
        }
        
        $printers = $client->viewPrinters(0,10, null, $computerId);
        
        $this->assertTrue(\is_array($printers));
        
        $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\Printer', $printers);
        
        foreach ($printers as $printerIndex => $printer) {
            
            $this->assertTrue(\in_array($printer->name, $expectedPrinterNames));
            
        }
        
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