<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use InvalidArgumentException;

final class MailerServiceFactory
{
    /**
     * @var string Empty mailer service identifier.
     */
    public const MAILER_SERVICE_EMPTY   = 'empty';

    /**
     * @var string The default PHP mailer service identifier.
     */
    public const MAILER_SERVICE_NATIVE  = 'native';

    /**
     * Gets an EmailSenderInterface from an identifier.
     *
     * @param string $identifier        The identifier to get the EmailSenderInterface for.
     *
     * @return EmailSenderInterface     The EmailSenderInterface.
     * @throws InvalidArgumentException If the identifier could not be recognized.
     */
    public function getMailerServiceFromIdentifier(string $identifier): EmailSenderInterface
    {
        switch ($identifier) {
            case self::MAILER_SERVICE_NATIVE:
                return new NativeMailerService();
            case self::MAILER_SERVICE_EMPTY:
                return new EmptyMailerService();
            default:
                throw new InvalidArgumentException(
                    "Identifier {$identifier} not recognized."
                );
        }
    }
}
