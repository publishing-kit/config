<?php

declare(strict_types=1);

namespace Tests;

use PublishingKit\Config\Config;

class ConfigTest extends SimpleTestCase
{
    public function testGetSetValue()
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertEquals('bar', $item->foo);
    }
    
    public function testGetUnsetValue()
    {
        $item = new Config([]);
        $this->assertNull($item->foo);
    }

    public function testImplementsCountable()
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf('Countable', $item);
        $this->assertCount(1, $item);
    }

    public function testImplementsArrayAccess()
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf('ArrayAccess', $item);
        $this->assertEquals('bar', $item['foo']);
    }

    public function testImplementsIteratorAggregate()
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf('IteratorAggregate', $item);
        $this->assertInstanceOf('ArrayIterator', $item->getIterator());
    }
}
