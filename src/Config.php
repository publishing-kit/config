<?php

declare(strict_types=1);

namespace PublishingKit\Config;

use Countable;

class Config implements Countable
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

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->config);
    }
}
