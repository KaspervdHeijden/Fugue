<?php

declare(strict_types=1);

namespace Fugue\CronJob;

use Fugue\Configuration\Config;
use InvalidArgumentException;

final class CronJobFactory
{
    /** @var Config $config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Gets a CronJob from an identifier.
     *
     * @param string   $identifier The identifier to get the CronJobInterface for.
     * @param string[] $arguments  The arguments passed from the CLI.
     *
     * @return CronJobInterface    The CronJob.
     */
    public function getCronJobFromIdentifier(string $identifier, array $arguments): CronJobInterface
    {
        throw new InvalidArgumentException("Identifier {$identifier} not recognized.");
    }
}
