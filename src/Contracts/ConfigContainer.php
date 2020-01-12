<?php

namespace PublishingKit\Config\Contracts;

interface ConfigContainer
{
    public static function fromFile(string $path): ConfigContainer;

    public static function fromFiles(array $files): ConfigContainer;

    /**
     * @return null|string|ConfigContainer
     */
    public function get(string $name);

    public function has(string $name): bool;
}
