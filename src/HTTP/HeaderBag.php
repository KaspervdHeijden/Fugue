<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Fugue\Collection\CollectionMap;
use DateTimeInterface;
use DateTimeImmutable;

use function is_string;

final class HeaderBag extends CollectionMap
{
    public function setCaching(
        bool $enableCaching,
        ?DateTimeInterface $lastModified,
        int $maxAge
    ): void {
        if ($enableCaching) {
            if ($lastModified > 0) {
                $this->setFromKeyValue('last_modified', $lastModified->format('D, d M Y H:i:s') . ' GTM');
            }

            $this->setFromKeyValue('cache_control', 'private' . ($maxAge > 0 ? ",max-age={$maxAge}" : ''));
            $this->unset('expires', 'pragma');
        } else {
            $this->setFromKeyValue('expires', (new DateTimeImmutable('-3 hours'))->format('D, d M Y H:i:s') . ' GTM');
            $this->setFromKeyValue('cache_control', 'no-store,no-cache');
            $this->setFromKeyValue('pragma', 'no-cache');
            $this->unset('last_modified');
        }
    }

    public function setFromKeyValue(string $key, string $value): void
    {
        $header = new Header($key, $value);
        $this->set($header, $header->getKey());
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
