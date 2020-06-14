<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\HTTP\Routing\RouteMatcher;
use Fugue\Collection\PropertyBag;

use const ENT_QUOTES;
use const ENT_HTML5;
use function htmlspecialchars;
use function ob_get_clean;
use function is_readable;
use function preg_match;
use function is_string;
use function ob_start;
use function extract;
use function is_file;

final class PHPTemplateAdapter implements TemplateInterface
{
    private RouteMatcher $matcher;
    private string $rootDir;

    public function __construct(RouteCollectionMap $routeMap, string $rootDir)
    {
        $this->matcher = new RouteMatcher($routeMap);
        $this->rootDir = $rootDir;
    }

    /**
     * Outputs an escaped value.
     *
     * @param mixed $text The text to escape.
     * @return string     The escaped version of the supplied text.
     */
    public function escape($text): string
    {
        return htmlspecialchars((string)$text, ENT_HTML5 | ENT_QUOTES);
    }

    /**
     * Displays a route URL.
     *
     * @param string $routeName  The name of the route.
     * @param array  $parameters The parameters for the route.
     *
     * @return string            The URL.
     */
    public function route(string $routeName, array $parameters = []): string
    {
        return $this->matcher->getUrl($routeName, $parameters);
    }

    public function supports(string $name): bool
    {
        if (! (bool)preg_match('/\.php$/', $name)) {
            return false;
        }

        $fileName = "{$this->rootDir}/{$name}";
        if (! is_file($fileName) || ! is_readable($fileName)) {
            return false;
        }

        return true;
    }

    public function render(string $templateName, PropertyBag $variables): string
    {
        if (! $this->supports($templateName)) {
            throw InvalidTemplateException::forUnrecognizedTemplateName($templateName);
        }

        $templateVariables = array_merge($variables->toArray(), ['view' => $this]);
        return (static function (
            string $currentTemplateFileName,
            array $templateVariables
        ): string {
            ob_start();

            extract($templateVariables);
            unset($templateVariables);

            /** @noinspection PhpIncludeInspection */
            include $currentTemplateFileName;

            $content = ob_get_clean();
            if (! is_string($content)) {
                return '';
            }

            return $content;
        })("{$this->rootDir}/{$templateName}", $templateVariables);
    }
}
