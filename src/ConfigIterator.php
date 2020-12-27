<?php

declare(strict_types=1);

namespace PublishingKit\Config;

use ArrayIterator;

final class ConfigIterator extends ArrayIterator
{
    /**
     * @return Config|scalar
     */
    public function current()
    {
        /** @var array|scalar **/
        $result = parent::current();
        if (is_array($result)) {
            return new Config($result);
        }
        return $result;
    }
}
