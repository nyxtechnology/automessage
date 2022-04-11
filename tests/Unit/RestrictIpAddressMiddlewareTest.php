<?php

namespace Tests\Unit;

use App\Http\Middleware\RestrictIpAddressMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RestrictIpAddressMiddlewareTest extends TestCase
{
    /**
     * @group ipMiddleware
     */
    public function testHandle()
    {
        //arrange
        $allowedIps = Config::get('settings.allowed_ips');
        $ipTest = '0.1.2.3';
        Config::set('settings.allowed_ips', [$ipTest]);
        $request = new Request();
        $middleware = new RestrictIpAddressMiddleware();
        //act
        $response = $middleware->handle($request, function () {});
        //assert
        $this->assertEquals($response->getStatusCode(), 403);

        //arrange
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $ipTest;
        //act
        $response = $middleware->handle($request, function () {});
        //assert
        $this->assertNull($response);

        Config::set('settings.allowed_ips', $allowedIps);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    /**
     * @group ipMiddleware
     */
    public function testGetClientIP()
    {
        //arrange
        $middleware = new RestrictIpAddressMiddleware();
        $serverIP = null;
        //act
        $ip = $middleware->getClientIP();
        //assert
        $this->assertEquals($serverIP, $ip);

        //arrange
        $serverIP = '01.01.01';
        $_SERVER['HTTP_CLIENT_IP'] = $serverIP;
        //act
        $ip = $middleware->getClientIP();
        //assert
        $this->assertEquals($serverIP, $ip);

        //arrange
        $serverIP = '01.01.02';
        $_SERVER['REMOTE_ADDR'] = $serverIP;
        //act
        $ip = $middleware->getClientIP();
        //assert
        $this->assertEquals($serverIP, $ip);

        //arrange
        $serverIP = '01.01.03';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $serverIP;
        //act
        $ip = $middleware->getClientIP();
        //assert
        $this->assertEquals($serverIP, $ip);
    }
}
