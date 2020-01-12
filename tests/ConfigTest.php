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
}
