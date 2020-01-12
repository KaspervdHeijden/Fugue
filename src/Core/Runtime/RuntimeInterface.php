<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\HTTP\Request;

interface RuntimeInterface
{
    /**
     * @var string The charset used throughout the application.
     */
    public const CHARSET = 'UTF-8';

    /**
     * Handles a Request.
     *
     * @param Request $request The request to handle.
     */
    public function handle(Request $request): void;
}
