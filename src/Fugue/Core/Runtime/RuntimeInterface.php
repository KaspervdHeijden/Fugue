<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\HTTP\Request;

interface RuntimeInterface
{
    public function handle(Request $request): void;
}
