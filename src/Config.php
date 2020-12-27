<?php

declare(strict_types=1);

namespace PublishingKit\Config;

use Countable;
use ArrayAccess;
use IteratorAggregate;
use PublishingKit\Config\Exceptions\ConfigDoesNotExist;
use PublishingKit\Config\Exceptions\UnsupportedConfigFileType;
use PublishingKit\Config\Contracts\ConfigContainer;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @psalm-consistent-constructor
 * @psalm-immutable
 */
class Config implements ArrayAccess, Countable, IteratorAggregate, ConfigContainer
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function fromFile(string $path): ConfigContainer
    {
        return new static(self::getFile($path));
    }

    public static function fromFiles(array $files): ConfigContainer
    {
        $configs = [];
        foreach ($files as $file) {
            $configs = array_merge($configs, self::getFile($file));
        }
        return new static($configs);
    }

    public static function __set_state(array $config): ConfigContainer
    {
        return new static($config);
    }

    private static function getFile(string $path): array
    {
        if (!file_exists($path)) {
            throw new ConfigDoesNotExist();
        }
        try {
            switch (pathinfo($path)['extension']) {
                case 'php':
                    $config = self::parseArrayFile($path);
                    break;
                case 'ini':
                    $config = self::parseIniFile($path);
                    break;
                case 'yml':
                case 'yaml':
                    $config = self::parseYamlFile($path);
                    break;
                default:
                    throw new UnsupportedConfigFileType(pathinfo($path)['extension']);
            }
        } catch (UnsupportedConfigFileType $e) {
            throw $e;
        }
        return $config;
    }

    private static function parseArrayFile(string $path): array
    {
        return include $path;
    }

    private static function parseIniFile(string $path): array
    {
        return parse_ini_file($path, true);
    }

    private static function parseYamlFile(string $path): array
    {
        return Yaml::parseFile($path);
    }

    /**
     * @param string $name
     * @return string|null|Config
     */
    public function get(string $name)
    {
        if (!isset($this->config[$name])) {
            return null;
        }
        if (is_array($this->config[$name])) {
            return new static($this->config[$name]);
        }
        return $this->config[$name];
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }

    public function has(string $name): bool
    {
        return isset($this->config[$name]);
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
        if (!isset($this->config[$offset])) {
            return null;
        }
        if (is_array($this->config[$offset])) {
            return new static($this->config[$offset]);
        }
        return $this->config[$offset];
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

    public function toArray(): array
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ConfigIterator($this->config);
    }
}
