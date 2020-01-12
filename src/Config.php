<?php

declare(strict_types=1);

namespace PublishingKit\Config;

class Config
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }
}
