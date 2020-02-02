<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Fugue\Collection\CollectionMap;
use InvalidArgumentException;

use function mb_strtolower;
use function array_map;
use function explode;
use function lcfirst;
use function ucfirst;

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
    public const NAME_EXPIRES = 'expires';

    /** @var string[] */
    private $parts;

    /** @var string */
    private $key;

    public function __construct(string $key, string $value)
    {
        if ($key === '') {
            throw new InvalidArgumentException('header name must not be empty.');
        }

        $parts = new CollectionMap([], 'string');
        foreach (explode(';', trim($value)) as $part) {
            foreach (explode('=', trim($part), 2) as $name => $value) {
                $parts[$name] = $value;
            }
        }

        $this->key   = trim(mb_strtolower($key));
        $this->parts = $parts;
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
        return implode(';', $this->parts->all());
    }

    public function toHeaderString(): string
    {
        $value = $this->getValue();

        if ($value !== '') {

        }

        return "{$this->getName()}: {$this->getValue()}";
    }

    public function __toString(): string
    {
        return $this->toHeaderString();
    }
}
