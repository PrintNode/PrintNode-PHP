<?php

use PrintNode\Request;
use PrintNode\Credentials\ApiKey;
use PrintNode\Credentials\Username;

class AuthTests extends PHPUnit_Framework_TestCase
{

    public function testAuthWithApiKey()
    {
        $credentials = new ApiKey(API_KEY, [], ['X-No-Throttle' => 'true']);
        $request = new Request($credentials);
        $whoami = $request->getWhoami();
        $this->assertSame($whoami->email, 'god@printnode.com');
    }

    public function testAuthWithAccountCredentials()
    {
        $credentials = new Username('matthew@printnode.com', 'matthew', [], ['X-No-Throttle' => 'true']);
        $request = new Request($credentials);
        $whoami = $request->getWhoami();
        $this->assertSame($whoami->email, 'matthew@printnode.com');
    }

    public function testChildAccountById()
    {
        $credentials = new ApiKey(API_KEY, ['id' => 444], ['X-No-Throttle' => 'true']);
        $request = new Request($credentials);
        $whoami = $request->getWhoami();
        $this->assertSame($whoami->email, 'matthew@printnode.com');
    }

    public function testChildAccountByEmail()
    {
        $credentials = new ApiKey(API_KEY, ['email' => 'matthew@printnode.com'], ['X-No-Throttle' => 'true']);
        $request = new Request($credentials);
        $whoami = $request->getWhoami();
        $this->assertSame($whoami->email, 'matthew@printnode.com');
    }

    public function testChildAccountByRef()
    {
        $credentials = new ApiKey(API_KEY, ['creatorRef' => 'matthew'], ['X-No-Throttle' => 'true']);
        $request = new Request($credentials);
        $whoami = $request->getWhoami();
        $this->assertSame($whoami->email, 'matthew@printnode.com');
    }

}