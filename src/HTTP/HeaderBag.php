<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Fugue\Collection\CollectionMap;
use DateTimeInterface;
use DateTimeImmutable;

use function is_string;
use function is_int;

final class HeaderBag extends CollectionMap
{
    public function disableClientCaching(): void
    {
        $dateTime = new DateTimeImmutable('-3 hours');

        $this->set('expires', $dateTime->format('D, d M Y H:i:s') . ' GTM');
        $this->set('cache_control', 'no-store,no-cache');
        $this->set('pragma', 'no-cache');
        $this->unset('last_modified');
    }

    public function enableClientCaching(
        ?DateTimeInterface $lastModified,
        ?int $maxAge
    ): void {
        if ($lastModified instanceof DateTimeInterface) {
            $this->set(
                'last_modified',
                $lastModified->format('D, d M Y H:i:s') . ' GTM'
            );
        }

        $cacheControl = 'private';
        if (is_int($maxAge) && $maxAge > 0) {
            $cacheControl .= ',max-age=' . (string)$maxAge;
        }

        $this->set('cache_control', $cacheControl);
        $this->unset('expires', 'pragma');
    }

    public function set($value, $key = null): void
    {
        if (is_string($value)) {
            parent::set(
                new Header((string)$key, $value),
                $key
            );
        } else {
            parent::set($value, $key);
        }
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
