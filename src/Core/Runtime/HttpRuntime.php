<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\Localization\Implementation\DutchPhoneNumberDisplayAdapter;
use Fugue\Localization\Implementation\DutchNumberFormatter;
use Fugue\Localization\Implementation\DutchDateFormatter;
use Fugue\View\Templating\TemplateUtil;
use Fugue\HTTP\Routing\RouteMatcher;
use Fugue\HTTP\Routing\RouteMap;
use Fugue\Configuration\Config;
use Fugue\Core\FrameWork;
use Fugue\HTTP\Response;
use Fugue\HTTP\Request;

use function header_remove;
use function headers_sent;
use function header;
use function strlen;

final class HttpRuntime implements RuntimeInterface
{
    /** @var TemplateUtil */
    private $templateUtil;

    /** @var RouteMap */
    private $routeMap;

    /** @var Config */
    private $config;

    public function __construct(FrameWork $frameWork)
    {
        $routeMap           = new RouteMap($frameWork->loadConfigFile('routes'));
        $this->templateUtil = new TemplateUtil(
            new DutchPhoneNumberDisplayAdapter(), // @todo load from container
            new DutchNumberFormatter(),
            new DutchDateFormatter(),
            $routeMap
        );

        $this->config   = $frameWork->getConfig();
        $this->routeMap = $routeMap;
    }

    public function handle(Request $request): void
    {
        $matcher  = new RouteMatcher(
            $this->templateUtil,
            $this->config,
            $this->routeMap
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
            "{$request->getProtocol()} {$response->getStatusCode()} {$response->getStatusCodeText()}"
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
