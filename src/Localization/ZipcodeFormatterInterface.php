<?php

declare(strict_types=1);

namespace Fugue\Localization;

interface ZipcodeFormatterInterface
{
    /**
     * Formats a zip code.
     *
     * @param string $zipcode A zip code value to format.
     * @return string         The formatted zip code.
     */
    public function format(string $zipcode): string;

    /**
     * Gets the zip code regex pattern.
     *
     * @return string The regex pattern for the zip code.
     */
    public function getPattern(): string;
}
