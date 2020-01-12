<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Persistence\Database\DatabaseQueryAdapterInterface;

abstract class DatabaseRepository
{
    /** @var DatabaseQueryAdapterInterface */
    private $adapter;

    public function __construct(DatabaseQueryAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    final protected function getAdapter(): DatabaseQueryAdapterInterface
    {
        return $this->adapter;
    }
}
