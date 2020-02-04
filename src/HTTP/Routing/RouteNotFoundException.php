<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\HTTP\Request;
use Fugue\Core\Exception\FugueException;

final class RouteNotFoundException extends FugueException
{
    public static function forRequest(Request $request): self
    {
        throw new static(
            "Route not found for {$request->getMethod()} '{$request->getUrl()->getPath()}'."
        );
    }
}
