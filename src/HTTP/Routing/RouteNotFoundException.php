<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\Core\Exception\FugueException;
use Fugue\HTTP\Request;

final class RouteNotFoundException extends FugueException
{
    public static function forRequest(Request $request): self
    {
        return new self(
            "Route not found for {$request->getMethod()} '{$request->getUrl()->getPath()}'."
        );
    }
}
