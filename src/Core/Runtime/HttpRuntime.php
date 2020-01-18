<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\HTTP\Routing\RouteMatcher;
use Fugue\HTTP\Routing\RouteMap;
use Fugue\Collection\Map;
use Fugue\Core\FrameWork;
use Fugue\HTTP\Response;
use Fugue\HTTP\Request;

use function header_remove;
use function headers_sent;
use function header;
use function strlen;

final class HttpRuntime implements RuntimeInterface
{
    /** @var FrameWork */
    private $framework;

    public function __construct(FrameWork $frameWork)
    {
        $this->framework = $frameWork;
    }

    public function handle(Request $request): void
    {
        $mapping  = new Map($this->framework->loadConfiguration('object-mapping'));
        $routeMap = new RouteMap($this->framework->loadConfiguration('routes'));
        $matcher  = new RouteMatcher(
            $mapping,
            $this->framework->getContainer(),
            $routeMap
        );

        $response = $matcher->findAndRun($request);
        if (!  headers_sent()) {
            $this->sendHeaders($request, $response);
        }

        echo (string)$response->getContent();
    }

    /**
     * @param Request  $request  The Request
     * @param Response $response The Response
     */
    private function sendHeaders(Request $request, Response $response): void
    {
        $code = $response->getStatusCode();

        header_remove();
        foreach ($this->getHeaders($request, $response) as $header) {
            header($header, true, $code);
        }
    }

    private function getHeaders(Request $request, Response $response): array
    {
        $headers = [
            "{$request->getProtocol()} {$response->getStatusCode()} {$response->getStatusCodeText()}",
        ];

        foreach ($response->getHeaders() as $name => $value) {
            $headers[] = "{$name}: {$value}";
        }

        if ($this->shouldSendContentLength($request, $response)) {
            $headers[] = 'Content-Length: ' . (string)strlen($response->getContent());
        }

        return $headers;
    }

    private function shouldSendContentLength(Request $request, Response $response): bool
    {
        switch ($response->getStatusCode()) {
            case Response::HTTP_NOT_MODIFIED:
            case Response::HTTP_NO_CONTENT:
                return false;
        }

        if ($request->isHeadRequest() || $response->isInformational() || $response->isRedirect()) {
            return false;
        }

        return true;
    }
}
