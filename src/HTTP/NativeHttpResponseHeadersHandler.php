<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use function header_remove;
use function headers_sent;
use function header;

final class NativeHttpResponseHeadersHandler implements ResponseHeadersHandlerInterface
{
    public function sendHeaders(Request $request, Response $response): void
    {
        if (headers_sent()) {
            return;
        }

        $code    = $response->getStatusCode();
        $headers = $response->getHeaders();

        if ($this->shouldSendContentLength($request, $response)) {
            $headers[] = Header::contentLength($response->getContent()->size());
        }

        header_remove();
        header("{$request->getProtocol()} {$code} {$response->getStatusText()}", true, $code);

        foreach ($headers as $header) {
            header($header->toHeaderString(), true, $code);
        }
    }

    private function shouldSendContentLength(
        Request $request,
        Response $response
    ): bool {
        switch ($response->getStatusCode()) {
            case Response::HTTP_NOT_MODIFIED:
            case Response::HTTP_NO_CONTENT:
                return false;
        }

        switch (true) {
            case $response->isInformational():
            case $request->isHeadRequest():
            case $response->isRedirect():
                return false;
        }

        return true;
    }
}
