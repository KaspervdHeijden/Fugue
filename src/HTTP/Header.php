<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use DateTimeInterface;
use Fugue\Collection\CollectionMap;
use InvalidArgumentException;

use function mb_strtolower;
use function array_map;
use function explode;
use function implode;
use function lcfirst;
use function ucfirst;
use function trim;

final class Header
{
    /** @var string */
    public const NAME_CONTENT_DISPOSITION = 'content_disposition';

    /** @var string */
    public const NAME_CONTENT_TYPE = 'content_type';

    /** @var string */
    public const NAME_LAST_MODIFIED = 'last_modified';

    /** @var string */
    public const NAME_CONTENT_LENGTH = 'content_length';

    /** @var string */
    public const NAME_CACHE_CONTROL = 'cache_control';

    /** @var string */
    public const NAME_LOCATION = 'location';

    /** @var string */
    public const NAME_EXPIRES = 'expires';

    private CollectionMap $parts;
    private string $key;

    public function __construct(string $key, string $value)
    {
        $this->key = trim(mb_strtolower($key));
        if ($this->key === '') {
            throw new InvalidArgumentException(
                'Header name must not be empty.'
            );
        }

        $this->parts = new CollectionMap([], 'string');
        foreach (explode(';', trim($value)) as $part) {
            foreach (explode('=', trim($part), 2) as $name => $value) {
                $this->parts[$name] = $value;
            }
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        $parts = array_map(
            static function (string $part): string {
                return ucfirst($part);
            },
            explode('_', $this->key)
        );

        return lcfirst(implode('-', $parts));
    }

    public function getValueParts(): CollectionMap
    {
        return $this->parts;
    }

    public function getValue(): string
    {
        return implode(';', $this->parts->toArray());
    }

    public function toHeaderString(): string
    {
        return "{$this->getName()}: {$this->getValue()}";
    }

    public function __toString(): string
    {
        return $this->toHeaderString();
    }

    public static function contentDisposition(string $disposition): self
    {
        return new static(
            self::NAME_CONTENT_DISPOSITION,
            $disposition
        );
    }

    public static function contentType(string $contentType): self
    {
        return new static(self::NAME_CONTENT_TYPE, $contentType);
    }

    public static function lastModified(DateTimeInterface $lastModified): self
    {
        return new static(
            self::NAME_CONTENT_TYPE,
            $lastModified->format('D, d M Y H:i:s') . ' GTM'
        );
    }

    public static function contentLength(int $length): self
    {
        return new static(
            self::NAME_CONTENT_LENGTH,
            (string)$length
        );
    }

    public static function cacheControl(string $cacheControl): self
    {
        return new static(
            self::NAME_CACHE_CONTROL,
            $cacheControl
        );
    }

    public static function location(string $location): self
    {
        return new static(self::NAME_LOCATION, $location);
    }

    public static function expires(DateTimeInterface $expires): self
    {
        return new static(
            self::NAME_CONTENT_TYPE,
            $expires->format('D, d M Y H:i:s') . ' GTM'
        );
    }
}
