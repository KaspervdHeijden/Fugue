<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\Localization\Formatting\Number\NumberFormatterInterface;
use Fugue\Localization\Formatting\Date\DateFormatterInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\HTTP\Routing\RouteMatcher;
use Fugue\Collection\PropertyBag;

use function htmlspecialchars;
use function ob_get_clean;
use function is_readable;
use function preg_match;
use function mb_strlen;
use function mb_substr;
use function is_string;
use function ob_start;
use function extract;
use function is_file;

final class PHPTemplateAdapter implements TemplateInterface
{
    private NumberFormatterInterface $numberFormatter;

    private DateFormatterInterface $dateFormatter;

    private OutputHandlerInterface $outputHandler;

    private RouteCollectionMap $routeMap;

    private string $rootDir;

    public function __construct(
        NumberFormatterInterface $numberFormatter,
        DateFormatterInterface $dateFormatter,
        OutputHandlerInterface $outputHandler,
        RouteCollectionMap $routeMap,
        string $rootDir
    ) {
        $this->numberFormatter = $numberFormatter;
        $this->dateFormatter   = $dateFormatter;
        $this->outputHandler   = $outputHandler;
        $this->routeMap        = $routeMap;
        $this->rootDir         = $rootDir;
    }

    /**
     * Outputs an escaped value.
     *
     * @param mixed $text   The text to escape.
     * @param bool  $output Whether to output the escaped text. Defaults to TRUE.
     *
     * @return string       The escaped version of the supplied text.
     */
    public function escape($text, bool $output = true): string
    {
        $escapedText = htmlspecialchars((string)$text, ENT_HTML5 | ENT_QUOTES);
        if ($output) {
            $this->output($escapedText);
        }

        return $escapedText;
    }

    /**
     * Outputs a formatted numeric value.
     *
     * @param mixed $numericValue The number to format.
     * @param int   $precision    The precision.
     * @param bool  $output       Whether or or not to output the result.
     *
     * @return string             The formatted number.
     */
    public function number(
        $numericValue,
        int $precision = 2,
        bool $output = true
    ): string {
        $formattedNumber = $this->numberFormatter->format($numericValue, $precision);
        if ($output) {
            $this->escape($formattedNumber);
        }

        return $formattedNumber;
    }

    /**
     * Outputs a formatted date.
     *
     * @param mixed $dateValue A date or datetime.
     * @param bool  $output    Whether or or not to output the result.
     *
     * @return string          The formatted date.
     */
    public function date($dateValue, bool $output = true): string
    {
        $formattedDate = $this->dateFormatter->format($dateValue);
        if ($output) {
            $this->escape($formattedDate);
        }

        return $formattedDate;
    }

    /**
     * Directly outputs text.
     *
     * @param mixed $text The string to output directly.
     */
    public function output($text): void
    {
        $this->outputHandler->write((string)$text);
    }

    /**
     * Shortens a long string.
     *
     * @param mixed $longString The string to shorten.
     * @param int   $maxLength  The maximum length of the resulting string.
     * @param bool  $output     Whether or or not to output the result.
     *
     * @return string           The optionally shorted text.
     */
    public function shorten(
        $longString,
        int $maxLength = 32,
        bool $output = true
    ): string {
        $length = mb_strlen((string)$longString);
        if ($length < $maxLength) {
            $shortString = (string)$longString;
        } elseif ($length >= 3) {
            $shortString = mb_substr((string)$longString, 0, $maxLength - 3) . '...';
        } else {
            $shortString = mb_substr((string)$longString, 0, $maxLength);
        }

        if ($output) {
            $this->escape($shortString);
        }

        return $shortString;
    }

    /**
     * Displays a route URL.
     *
     * @param string $routeName  The name of the route.
     * @param array  $parameters The parameters for the route.
     * @param bool   $output     Whether or not to output the result.
     *
     * @return string            The URL.
     */
    public function route(
        string $routeName,
        array $parameters = [],
        bool $output = true
    ): string {
        $matcher = new RouteMatcher($this->routeMap);
        $url     = $matcher->getUrl($routeName, $parameters);

        if ($output) {
            $this->escape($url);
        }

        return $url;
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
            string $templateFileName,
            array $templateVariables
        ): string {
            ob_start();

            extract($templateVariables);
            unset($templateVariables);

            /** @noinspection PhpIncludeInspection */
            include $templateFileName;

            $content = ob_get_clean();
            if (! is_string($content)) {
                return '';
            }

            return $content;
        })("{$this->rootDir}/{$templateName}", $templateVariables);
    }
}
