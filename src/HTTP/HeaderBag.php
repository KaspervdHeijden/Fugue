<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Fugue\Collection\CollectionMap;
use DateTimeInterface;
use DateTimeImmutable;

use function is_string;

final class HeaderBag extends CollectionMap
{
    public function disableClientCaching(): void
    {
        $dateTime = new DateTimeImmutable('-3 hours');

        $this->unset('last_modified');
        $this->push([
            'expires'       => "{$dateTime->format('D, d M Y H:i:s')} GMT",
            'cache_control' => 'no-store,no-cache',
            'pragma'        => 'no-cache',
        ]);
    }

    public function enableClientCaching(
        ?DateTimeInterface $lastModified,
        ?int $maxAge
    ): void {
        if ($lastModified instanceof DateTimeInterface) {
            $this['last_modified'] = "{$lastModified->format('D, d M Y H:i:s')} GTM";
        }

        $cacheControl = 'private';
        if ($maxAge > 0) {
            $cacheControl .= ',max-age=' . (string)$maxAge;
        }

        $this['cache_control'] = $cacheControl;
        $this->unset('expires', 'pragma');
    }

    public function set($value, $key = null): void
    {
        $header = $value;
        if (! $header instanceof Header) {
            $header = new Header((string)$key, (string)$value);
        }

        parent::set($header, $header->getKey());
    }

    protected function checkKey($key): bool
    {
        return is_string($key);
    }

    protected function checkValue($value): bool
    {
        return $value instanceof Header;
    }
}
