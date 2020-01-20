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

    public function testImplementsIterator()
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf('IteratorAggregate', $item);
    }

    public function testReturnsIterator()
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf('PublishingKit\Config\ConfigIterator', $item->getIterator());
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

    public function testGet()
    {
        $config = [
            'foo' => [
                'bar' => 'baz'
            ]
        ];
        $item = new Config($config);
        $this->assertInstanceOf(Config::class, $item->get('foo'));
        $this->assertEquals('baz', $item->get('foo')->get('bar'));
    }

    public function testHas()
    {
        $config = [
            'foo' => [
                'bar' => 'baz'
            ]
        ];
        $item = new Config($config);
        $this->assertTrue($item->has('foo'));
        $this->assertTrue($item->get('foo')->has('bar'));
        $this->assertFalse($item->get('foo')->has('baz'));
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

    public function testGetConfigFromNonExistentFile()
    {
        $this->expectException(ConfigDoesNotExist::class);
        $item = Config::fromFile('tests/no-config.php');
    }

    public function testGetConfigFromUnsupportedSource()
    {
        $this->expectException(UnsupportedConfigFileType::class);
        $item = Config::fromFile('tests/config.wibble');
    }

    public function testGetConfigFromPhpFile()
    {
        $item = Config::fromFile('tests/config.php');
        $this->assertEquals('bar', $item->foo);
    }

    public function testGetConfigFromIniFile()
    {
        $item = Config::fromFile('tests/config.ini');
        $this->assertEquals('bar', $item->values->foo);
    }

    public function testGetConfigFromYamlFile()
    {
        $item = Config::fromFile('tests/config.yml');
        $this->assertEquals('filesystem', $item->cache->driver);
        $this->assertEquals('cache/data', $item->cache->path);
    }

    public function testGetConfigFromMultipleFiles()
    {
        $item = Config::fromFiles([
            'tests/config.yml',
            'tests/config.ini',
            'tests/config.php',
        ]);
        $this->assertEquals('bar', $item->foo);
        $this->assertEquals('bar', $item->values->foo);
        $this->assertEquals('filesystem', $item->cache->driver);
        $this->assertEquals('cache/data', $item->cache->path);
    }
}
