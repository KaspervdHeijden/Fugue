<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\Localization\Formatting\PhoneNumber\PhoneNumberFormatterInterface;
use Fugue\Localization\Formatting\Number\NumberFormatterInterface;
use Fugue\Localization\Formatting\Date\DateFormatterInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\HTTP\Routing\Route;

use function realpath;
use function rtrim;

final class TemplateUtil
{
    /** @var PhoneNumberFormatterInterface */
    private $phoneNumberFormatter;

    /** @var NumberFormatterInterface */
    private $numberFormatter;

    /** @var DateFormatterInterface */
    private $dateFormatter;

    /** @var OutputHandlerInterface */
    private $outputHandler;

    /** @var RouteCollectionMap */
    private $routeMap;

    public function __construct(
        PhoneNumberFormatterInterface $phoneNumberFormatter,
        NumberFormatterInterface $numberFormatter,
        DateFormatterInterface $dateFormatter,
        OutputHandlerInterface $outputHandler,
        RouteCollectionMap $routeMap
    ) {
        $this->phoneNumberFormatter = $phoneNumberFormatter;
        $this->numberFormatter      = $numberFormatter;
        $this->outputHandler        = $outputHandler;
        $this->dateFormatter        = $dateFormatter;
        $this->routeMap             = $routeMap;
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
    public function number($numericValue, int $precision = 2, bool $output = true): string
    {
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
    public function shorten($longString, int $maxLength = 32, bool $output = true): string
    {
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
    public function route(string $routeName, array $parameters = [], bool $output = true): string
    {
        $route = $this->routeMap->get($routeName);
        if (! $route instanceof Route) {
            return '';
        }

        $url = $route->getURL($parameters);
        if ($output) {
            $this->escape($url);
        }

        return $url;
    }

    /**
     * Outputs a formatted phone number.
     *
     * @param mixed $phoneNumber The phone number.
     * @param bool  $output      Whether or or not to output the result.
     *
     * @return string            The formatted phone number.
     */
    public function phone($phoneNumber, bool $output): string
    {
        $formattedPhoneNumber = $this->phoneNumberFormatter->format($phoneNumber);
        if ($output) {
            $this->output($formattedPhoneNumber);
        }

        return $formattedPhoneNumber;
    }

    public function getTemplateRootDir(): string
    {
        $rootDir = rtrim(realpath(__DIR__ . '/../../../'), '/');
        return "{$rootDir}/templates/";
    }
}
