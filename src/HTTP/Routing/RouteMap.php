<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\Collection\ArrayMap;

use function is_string;

final class RouteMap extends ArrayMap
{
    protected function checkKey($key): bool
    {
        if (! is_string($key)) {
            return false;
        }

        return true;
    }

    protected function checkValue($value): bool
    {
        return $value instanceof Route;
    }
}
