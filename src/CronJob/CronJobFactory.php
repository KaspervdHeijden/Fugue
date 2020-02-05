<?php

declare(strict_types=1);

namespace Fugue\CronJob;

use InvalidArgumentException;

final class CronJobFactory
{
    /**
     * Gets a CronJob from an identifier.
     *
     * @param string   $identifier The identifier to get the CronJobInterface for.
     * @return CronJobInterface    The CronJob.
     */
    public function getCronJobFromIdentifier(
        string $identifier
    ): CronJobInterface {
        throw new InvalidArgumentException(
            "Identifier {$identifier} not recognized."
        );
    }
}
