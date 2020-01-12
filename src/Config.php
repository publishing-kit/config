<?php

declare(strict_types=1);

namespace PublishingKit\Config;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use PublishingKit\Config\Exceptions\ConfigCouldNotBeParsed;
use PublishingKit\Config\Exceptions\ConfigDoesNotExist;
use PublishingKit\Config\Exceptions\UnsupportedConfigFileType;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

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

    public static function fromFile(string $path): self
    {
        return new static(self::getFile($path));
    }

    public static function fromFiles(array $files): self
    {
        $configs = [];
        foreach ($files as $file) {
            $configs = array_merge($configs, self::getFile($file));
        }
        return new static($configs);
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
        } catch (\Exception $e) {
            throw new ConfigCouldNotBeParsed('File ' . $path . ' could not be parsed');
        }
        return $config;
    }

    private static function parseArrayFile(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }
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

    public function toArray(): array
    {
        return $this->config;
    }
}
