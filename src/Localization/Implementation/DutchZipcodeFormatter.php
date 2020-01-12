<?php

declare(strict_types=1);

namespace Fugue\Localization\Implementation;

use Fugue\Localization\ZipcodeFormatterInterface;
use InvalidArgumentException;

use function mb_strtoupper;
use function preg_replace;
use function preg_match;
use function trim;

final class DutchZipcodeFormatter implements ZipcodeFormatterInterface
{
    /** @var string */
    private const ZIPCODE_PATTERN_DUTCH = '^\d{4} ?[A-Za-z]{2}$';

    public function format(string $zipcode): string
    {
        $trimmedZipcode = trim($zipcode);
        if ((bool)preg_match("/{$this->getPattern()}/", $trimmedZipcode)) {
            return mb_strtoupper(preg_replace('/^(\d{4})([A-Za-z]{2}$)/u', '\1 \2', $trimmedZipcode));
        }

        throw new InvalidArgumentException('Invalid zipcode format.');
    }

    public function getPattern(): string
    {
        return self::ZIPCODE_PATTERN_DUTCH;
    }
}
