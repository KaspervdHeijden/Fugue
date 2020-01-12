<?php /** @noinspection SpellCheckingInspection */

declare(strict_types=1);

namespace Fugue\Localization\Implementation;

final class DutchDateFormatter extends DateFormatter
{
    /**
     * @var string|int The date format used.
     *
     * @see http://userguide.icu-project.org/formatparse/datetime
     */
    private const DATE_FORMAT = 'cccc d LLLL yyyy';

    public function format($date): string
    {
        if (! $date) {
            return '';
        }

        $stamp     = $this->getDateTime($date);
        $formatter = $this->getFormatter('nl_NL', self::DATE_FORMAT);

        return $formatter->format($stamp);
    }
}
