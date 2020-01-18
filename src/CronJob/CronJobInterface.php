<?php

declare(strict_types=1);

namespace Fugue\CronJob;

interface CronJobInterface
{
    /**
     * Calls the CronJob.
     *
     * @param string[] $arguments
     */
    public function run(array $arguments): void;
}
