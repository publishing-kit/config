<?php

declare(strict_types=1);

namespace Tests;

use PublishingKit\Config\Config;
use PublishingKit\Config\Exceptions\ConfigDoesNotExist;
use PublishingKit\Config\Exceptions\UnsupportedConfigFileType;

class ConfigTest extends SimpleTestCase
{
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

    public function testGetArrayValue()
    {
        $config = [
            'foo' => [
                'bar' => 'baz'
            ]
        ];
        $item = new Config($config);
        $this->assertInstanceOf(Config::class, $item->foo);
        $this->assertEquals('baz', $item->foo->bar);
    }

    public function testConvertToArray()
    {
        $config = [
            'foo' => [
                'bar' => 'baz'
            ]
        ];
        $item = new Config($config);
        $this->assertEquals($config, $item->toArray());
    }

    public function testGetConfigFromPhpFile()
    {
        $item = Config::fromFile('tests/config.php');
        $this->assertEquals('bar', $item->foo);
    }

    public function testGetNonExistentConfigFromPhpFile()
    {
        $this->expectException(ConfigDoesNotExist::class);
        $item = Config::fromFile('tests/no-config.php');
    }

    public function testGetConfigFromUnsupportedSource()
    {
        $this->expectException(UnsupportedConfigFileType::class);
        $item = Config::fromFile('tests/config.wibble');
    }

    public function testGetConfigFromIniFile()
    {
        $item = Config::fromFile('tests/config.ini');
        $this->assertEquals('bar', $item->values->foo);
    }
}
