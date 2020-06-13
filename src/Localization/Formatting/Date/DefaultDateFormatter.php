<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

class DefaultDateFormatter implements DateFormatterInterface
{
    public const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

    private string $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function format($value): string
    {
        if (! $value instanceof DateTimeInterface) {
            try {
                $value = new DateTimeImmutable($value);
            } catch (Exception $exception) {
                throw new InvalidArgumentException(
                    sprintf('Invalid date argument "%s"', (string)$value),
                    $exception->getCode(),
                    $exception
                );
            }
        }

        return $value->format($this->format);
    }

    public static function forMysql(): self
    {
        return new static(self::MYSQL_DATE_FORMAT);
    }
}
