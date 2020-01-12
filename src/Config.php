<?php

declare(strict_types=1);

namespace PublishingKit\Config;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use PublishingKit\Config\Exceptions\ConfigDoesNotExist;

/**
 * @psalm-immutable
 */
class Config implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function fromFile(string $path)
    {
        if (!file_exists($path)) {
            throw new ConfigDoesNotExist();
        }
        $config = self::parseArrayFile($path);
        return new static($config);
    }

    private static function parseArrayFile(string $path): array
    {
        return include $path;
    }

    public function __get(string $name)
    {
        if (!isset($this->config[$name])) {
            return null;
        }
        if (is_array($this->config[$name])) {
            return new static($this->config[$name]);
        }
        return $this->config[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->config);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->config);
    }
}
