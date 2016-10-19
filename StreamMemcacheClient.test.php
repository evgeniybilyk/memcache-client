<?php

require_once "StreamMemcacheClient.php";

class StreamMemcacheClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClassName()
    {
        $clientMemcache = new \StreamMemcacheClient("localhost:11211");
        $this->assertSame('StreamMemcacheClient', get_class($clientMemcache));
    }

    public function testSetItem()
    {
        $clientMemcache = new \StreamMemcacheClient("localhost:11211");
        $this->assertSame('STORED', $clientMemcache->set('myKey', 'myVal'));
    }

    public function testGetItem()
    {
        $clientMemcache = new \StreamMemcacheClient("localhost:11211");
        $clientMemcache->set('myKey', 'testGetItem');
        $this->assertSame('testGetItem', $clientMemcache->get('myKey'));
        $this->assertNotSame('myVal', $clientMemcache->get('myKey'));
    }

    public function testDeleteItem()
    {
        $clientMemcache = new \StreamMemcacheClient("localhost:11211");
        $clientMemcache->set('myKey', 'testDeleteItem');
        $this->assertSame('DELETED', $clientMemcache->delete('myKey'));
        $this->assertSame('NOT_FOUND', $clientMemcache->delete('myKey'));
    }
}