<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class ComputerScalesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testComputerScales()
    {
        
        $computerIds = array_values(printnodeSetUp());
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $scales = $client->viewScales(0);
        
        $this->assertTrue(\is_array($scales));
        
        $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\Scale', $scales);
        
        $scale = array_pop($scales);

        $this->assertObjectHasAttribute('mass', $scale);
        $this->assertObjectHasAttribute('deviceName', $scale);
        $this->assertObjectHasAttribute('deviceNum', $scale);
        $this->assertObjectHasAttribute('port', $scale);
        $this->assertObjectHasAttribute('count', $scale);
        $this->assertObjectHasAttribute('measurement', $scale);
        $this->assertObjectHasAttribute('clientReportedCreateTimestamp', $scale);
        $this->assertObjectHasAttribute('ntpOffset', $scale);
        $this->assertObjectHasAttribute('ageOfData', $scale);
        $this->assertObjectHasAttribute('computerId', $scale);
        $this->assertObjectHasAttribute('vendor', $scale);
        $this->assertObjectHasAttribute('product', $scale);
        $this->assertObjectHasAttribute('vendorId', $scale);
        $this->assertObjectHasAttribute('productId', $scale);

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