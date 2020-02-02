<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\HTTP\Request;

interface RuntimeInterface
{
    /**
     * @var string The charset used throughout the application.
     *             Please note that this is not a configuration setting,
     *             because I don't see a good reason to not use UTF-8.
     */
    public const CHARSET = 'UTF-8';

    /**
     * Handles a Request.
     *
     * @param Request $request The request to handle.
     */
    public function handle(Request $request): void;
}
