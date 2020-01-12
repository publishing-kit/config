<?php

declare(strict_types=1);

namespace Tests;

use PublishingKit\Config\Config;

class ConfigTest extends SimpleTestCase
{
    public function testCreate()
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertEquals('bar', $item->foo);
    }
}
