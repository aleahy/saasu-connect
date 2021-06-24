<?php


namespace Aleahy\SaasuConnect\Tests;

use Aleahy\SaasuConnect\SaasuAPI;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $api;
    protected $mockHandler;
    protected $fileId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileId = '123456';
        $this->mockHandler = new MockHandler();
        $client = new Client([
            'handler' => HandlerStack::create($this->mockHandler)
        ]);
        $this->api = new SaasuAPI($client, $this->fileId);
    }

}