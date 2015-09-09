<?php

if (!\function_exists('printnodeSetUp')) {
    require('setUpTearDown.php');
}

class ComputersTest extends PHPUnit_Framework_TestCase
{

    /**
     * Tests the retrieval of an array of computers on the active account and
     * tests that all properties are present on one of the returned computer 
     * objects in the array
     * @test
     */
    public function testViewComputers()
    {
    
        $computerIds = printnodeSetUp();
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $computers = $client->viewComputers();
        
        $this->assertTrue(\is_array($computers));
        
        $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\Computer', $computers);
        
        $computer = array_pop($computers);
        
        //Is the computer ID present in the array returned from printnodeSetUp?
        $this->assertContains($computer->id, $computerIds);
        
        $this->assertObjectHasAttribute('id', $computer);
        $this->assertObjectHasAttribute('name', $computer);
        $this->assertObjectHasAttribute('inet', $computer);
        $this->assertObjectHasAttribute('inet6', $computer);
        $this->assertObjectHasAttribute('hostname', $computer);
        $this->assertObjectHasAttribute('version', $computer);
        $this->assertObjectHasAttribute('jre', $computer);
        $this->assertObjectHasAttribute('systemInfo', $computer);
        $this->assertObjectHasAttribute('acceptOfflinePrintJobs', $computer);
        $this->assertObjectHasAttribute('createTimestamp', $computer);
        $this->assertObjectHasAttribute('state', $computer);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval only two computers, set by limit
     * @test
     */
    public function testViewLimitedComputers()
    {
        
        $computerIds = printnodeSetUp();
        
        $limitedIds= array_slice(array_values($computerIds), 0, 2);
        
        $this->subTestcomputerSetSubTest(0, 2, null, $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval only two computers, set by limit, with an offset
     * @test
     */
    public function testViewLimitedOffsetComputers()
    {
        
        $computerIds = printnodeSetUp();
        
        $limitedIds = array_values(array_slice(array_values($computerIds), 2, 3));
        
        $this->subTestcomputerSetSubTest(2, 100, null, $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval of one computer, where the argument is passed as
     * a string
     * @test
     */
    public function testViewComputerSetSingleByString()
    {
        
        $computerIds = printnodeSetUp();
        
        $limitedIds = array_values(array_slice(array_values($computerIds), 0, 1));
        
        $this->subTestcomputerSetSubTest(0, 100, $limitedIds[0], $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval of one computer, where the argument is passed as
     * a single-element array
     * @test
     */
    public function testViewComputerSetSingleByArray()
    {
        
        $computerIds = printnodeSetUp();
        
        $limitedIds = array_values(array_slice(array_values($computerIds), 0, 1));
        
        $this->subTestcomputerSetSubTest(0, 100, $limitedIds, $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval of two computers, where the argument is passed as
     * a comma-separated string
     * @test
     */
    public function testViewComputerSetMultiByString()
    {
        
        $computerIds = printnodeSetUp();
        
        $limitedIds = array_values(array_slice(array_values($computerIds), 3, 2));
        
        $this->subTestcomputerSetSubTest(0, 100, implode(',', $limitedIds), $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval of two computers, where the argument is passed as
     * a two-element array
     * @test
     */
    public function testViewComputerSetMultiByArray()
    {
    
        $computerIds = printnodeSetUp();
        
        $limitedIds = array_values(array_slice(array_values($computerIds), 3, 2));
        
        $this->subTestcomputerSetSubTest(0, 100, $limitedIds, $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval of two computers, where the set argument is passed as
     * a set string of '[SMALLEST_COMPUTER_ID]-'
     * @test
     */
    public function testViewComputerSet()
    {
        
        $computerIds = printnodeSetUp();
        
        $limitedIds = array_values(array_slice(array_values($computerIds), 3, 2));
        
        $this->subTestcomputerSetSubTest(0, 100, $limitedIds[0] . '-', $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Tests the retrieval of two computers where the the set string is invalid
     * @expectedException \PrintNode\Exception\HTTPException
     * @test
     */
    public function testViewComputerInvalidSet()
    {
        
        $computerIds = printnodeSetUp();
        
        $limitedIds = array_values(array_slice(array_values($computerIds), 3, 2));
        
        $this->subTestcomputerSetSubTest(0, 100, implode(', ', $limitedIds), $limitedIds);
        
        printnodeTearDown();
        
    }
    
    /**
     * Makes a request with a given offset, limit and optional 'set' string,
     * and checks the response from the server against the $expectedMatches
     * variable
     * 
     */
    public function subTestcomputerSetSubTest($offset, $limit, $set = null, $expectedMatches)
    {
        
        $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);
        
        $client = new \PrintNode\Client($credentials);
        
        $client->apiHost = API_HOST;
        
        $computers = $client->viewComputers($offset, $limit, $set);
        
        $this->assertContainsOnlyInstancesOf('\PrintNode\Entity\Computer', $computers);
        
        $this->assertEquals(sizeof($computers), sizeof($expectedMatches));
        
        $countIndex = 0;
        
        foreach ($computers as $computer) {
            
            $this->assertEquals($computer->id, $expectedMatches[$countIndex]);
            
            $countIndex++;
            
        }
        
        return true;
        
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