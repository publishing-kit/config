<?php

declare(strict_types=1);

namespace Tests;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use PublishingKit\Config\Config;
use PublishingKit\Config\ConfigIterator;
use PublishingKit\Config\Exceptions\ConfigDoesNotExist;
use PublishingKit\Config\Exceptions\UnsupportedConfigFileType;

final class ConfigTest extends SimpleTestCase
{
    public function testImplementsCountable(): void
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf(Countable::class, $item);
        $this->assertCount(1, $item);
    }

    public function testImplementsArrayAccess(): void
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf(ArrayAccess::class, $item);
        $this->assertEquals('bar', $item['foo']);
    }

    public function testImplementsIterator(): void
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf(IteratorAggregate::class, $item);
    }

    public function testReturnsIterator(): void
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertInstanceOf(ConfigIterator::class, $item->getIterator());
    }

    public function testGetSetValue(): void
    {
        $config = [
            'foo' => 'bar'
        ];
        $item = new Config($config);
        $this->assertEquals('bar', $item->foo);
    }

    public function testGetUnsetValue(): void
    {
        $item = new Config([]);
        $this->assertNull($item->foo);
    }

    public function testGetArrayValue(): void
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

    public function testGet(): void
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

    public function testHas(): void
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

    public function testConvertToArray(): void
    {
        $config = [
            'foo' => [
                'bar' => 'baz'
            ]
        ];
        $item = new Config($config);
        $this->assertEquals($config, $item->toArray());
    }

    public function testGetConfigFromNonExistentFile(): void
    {
        $this->expectException(ConfigDoesNotExist::class);
        $item = Config::fromFile('tests/no-config.php');
    }

    public function testGetConfigFromUnsupportedSource(): void
    {
        $this->expectException(UnsupportedConfigFileType::class);
        $item = Config::fromFile('tests/config.wibble');
    }

    public function testGetConfigFromPhpFile(): void
    {
        $item = Config::fromFile('tests/config.php');
        $this->assertEquals('bar', $item->foo);
    }

    public function testGetConfigFromNonexistentPhpFile(): void
    {
        $this->expectException(ConfigDoesNotExist::class);
        $item = $this->invokeMethod(new Config([]), 'parseArrayFile', ['path' => 'tests/bad-config.php']);
    }

    public function testGetConfigFromIniFile(): void
    {
        $item = Config::fromFile('tests/config.ini');
        $this->assertEquals('bar', $item->values->foo);
    }

    public function testGetConfigFromNonexistentIniFile(): void
    {
        $this->expectException(ConfigDoesNotExist::class);
        $item = $this->invokeMethod(new Config([]), 'parseIniFile', ['path' => 'tests/bad-config.ini']);
    }

    public function testGetConfigFromYamlFile(): void
    {
        $item = Config::fromFile('tests/config.yml');
        $this->assertEquals('filesystem', $item->cache->driver);
        $this->assertEquals('cache/data', $item->cache->path);
    }

    public function testGetConfigFromNonexistentYamlFile(): void
    {
        $this->expectException(ConfigDoesNotExist::class);
        $item = $this->invokeMethod(new Config([]), 'parseYamlFile', ['path' => 'tests/bad-config.yml']);
    }

    public function testGetConfigFromMultipleFiles(): void
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

    public function testMultipleLevelIterator(): void
    {
        $item = new Config([
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => 'baz'
        ]);
        $iterator = $item->getIterator();
        $this->assertInstanceOf(Config::class, $iterator->current());
        $iterator->next();
        $this->assertTrue(is_scalar($iterator->current()));
    }

    public function testOffsetExists(): void
    {
        $item = new Config([
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => 'baz'
        ]);
        $this->assertTrue($item->offsetExists('foo'));
        $this->assertFalse($item->offsetExists('baz'));
    }

    public function testOffsetGet(): void
    {
        $item = new Config([
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => 'baz'
        ]);
        $this->assertInstanceOf(Config::class, $item->offsetGet('foo'));
        $this->assertEquals('baz', $item->offsetGet('bar'));
        $this->assertNull($item->offsetGet('baz'));
    }

    public function testOffsetGetInvalidParam(): void
    {
        $this->expectException(ConfigDoesNotExist::class);
        $item = new Config([
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => 'baz'
        ]);
        $item->offsetGet([]);
    }

    public function testOffsetSet(): void
    {
        $item = new Config([
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => 'baz'
        ]);
        $this->assertNull($item->offsetSet('foo', 'bar'));
    }

    public function testOffsetUnset(): void
    {
        $item = new Config([
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => 'baz'
        ]);
        $this->assertNull($item->offsetUnset('foo'));
    }

    public function testSetState(): void
    {
        $config = Config::__set_state([
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => 'baz'
        ]);
        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('baz', $config->bar);
    }
}
