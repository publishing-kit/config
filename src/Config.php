<?php

declare(strict_types=1);

namespace PublishingKit\Config;

use PublishingKit\Config\Contracts\ConfigContainer;
use PublishingKit\Config\Exceptions\ConfigDoesNotExist;
use PublishingKit\Config\Exceptions\UnsupportedConfigFileType;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @psalm-consistent-constructor
 * @psalm-immutable
 */
class Config implements ConfigContainer
{
    /**
     * @var array
     * @psalm-var array<array-key, scalar|array>
     */
    private $config;

    /**
     * @param array $config
     * @psalm-param array<array-key, scalar|array> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function fromFile(string $path): ConfigContainer
    {
        return new static(self::getFile($path));
    }

    /**
     * @psalm-param $files array<array-key, string> Array of file paths
     */
    public static function fromFiles(array $files): ConfigContainer
    {
        $configs = [];
        /** @var string **/
        foreach ($files as $file) {
            $configs = array_merge($configs, self::getFile($file));
        }
        return new static($configs);
    }

    /**
     * @param array $config
     * @psalm-param array<array-key, scalar|array> $config
     */
    public static function __set_state(array $config): ConfigContainer
    {
        return new static($config);
    }

    /**
     * @return array
     * @psalm-return array<array-key, scalar|array>
     */
    private static function getFile(string $path): array
    {
        if (!$path = realpath($path)) {
            throw new ConfigDoesNotExist();
        }
        try {
            $ext = pathinfo($path)['extension'] ?? '';
            switch ($ext) {
                case 'php':
                    /** @var array<array-key, scalar|array> **/
                    $config = self::parseArrayFile($path);
                    break;
                case 'ini':
                    /** @var array<array-key, scalar|array> **/
                    $config = self::parseIniFile($path);
                    break;
                case 'yml':
                case 'yaml':
                    /** @var array<array-key, scalar|array> **/
                    $config = self::parseYamlFile($path);
                    break;
                default:
                    throw new UnsupportedConfigFileType($ext);
            }
        } catch (UnsupportedConfigFileType $e) {
            throw $e;
        }
        return $config;
    }

    private static function parseArrayFile(string $path): array
    {
        if (!file_exists($path)) {
            throw new ConfigDoesNotExist();
        }
        /** @var array **/
        return include $path;
    }

    private static function parseIniFile(string $path): array
    {
        if (!file_exists($path)) {
            throw new ConfigDoesNotExist();
        }
        /** @var array **/
        return parse_ini_file($path, true);
    }

    private static function parseYamlFile(string $path): array
    {
        if (!file_exists($path)) {
            throw new ConfigDoesNotExist();
        }
        /** @var array **/
        return Yaml::parseFile($path);
    }

    /**
     * @param string $name
     *
     * @return null|scalar|static
     */
    public function get(string $name)
    {
        if (!isset($this->config[$name])) {
            return null;
        }
        /** @var array<array-key, array|scalar>|scalar **/
        $config = $this->config[$name];
        if (is_array($config)) {
            return new static($config);
        }
        return $config;
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
     * @psalm-param scalar $offset
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * {@inheritDoc}
     * @param $offset scalar
     * @psalm-return Config|scalar|null
     * @psalm-assert scalar $offset
     */
    public function offsetGet($offset)
    {
        if (!is_scalar($offset)) {
            throw new ConfigDoesNotExist();
        }
        if (!isset($this->config[$offset])) {
            return null;
        }
        if (is_array($this->config[$offset])) {
            /** @var array<array-key, scalar|array> **/
            $config = $this->config[$offset];
            return new static($config);
        }
        /** @var scalar **/
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
