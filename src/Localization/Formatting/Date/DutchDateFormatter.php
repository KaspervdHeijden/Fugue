<?php /** @noinspection SpellCheckingInspection */

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

use function is_string;

final class DutchDateFormatter extends DateFormatter
{
    /**
     * @var string|int The date format used.
     *
     * @see http://userguide.icu-project.org/formatparse/datetime
     */
    private const DATE_FORMAT = 'cccc d LLLL yyyy';

    /** @var string */
    private const LANG_CODE_NL = 'nl_NL';

    public function format(string $date): string
    {
        if ($date === '') {
            return '';
        }

        $stamp     = $this->getDateTime($date);
        $formatter = $this->getFormatter(
            self::LANG_CODE_NL,
            self::DATE_FORMAT
        );

        $formattedDate = $formatter->format($stamp);
        if (! is_string($formattedDate)) {
            return '';
        }

        return $formattedDate;
    }
}
