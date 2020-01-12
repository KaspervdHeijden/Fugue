<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\Core\Exception\FugueException;
use Fugue\HTTP\Response;

final class RouteNotFoundException extends FugueException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
