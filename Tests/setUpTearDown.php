<?php

function printnodeSetUp()
{

    $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);

    $request = new \PrintNode\Request($credentials, 'test/data/generate', 'GET');

    $request->apiHost = 'apidev.printnode.com';

    $response = $request->process();

    return json_decode(json_encode($response->bodyJson), true);
        
}

function printnodeTearDown()
{

    $credentials = new \PrintNode\Credentials\ApiKey(API_KEY);

    $request = new \PrintNode\Request($credentials, 'test/data/generate', 'DELETE');

    $request->apiHost = 'apidev.printnode.com';

    return $request->process();

}