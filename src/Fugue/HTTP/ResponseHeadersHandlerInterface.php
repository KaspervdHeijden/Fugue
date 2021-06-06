<?php

declare(strict_types=1);

namespace Fugue\HTTP;

interface ResponseHeadersHandlerInterface
{
    public function sendHeaders(Request $request, Response $response): void;
}
