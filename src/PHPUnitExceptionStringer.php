<?php

/*
 * This file is part of the Behat Testwork.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\PHPUnitAssertionsExtension;

use Behat\Testwork\Exception\Stringer\ExceptionStringer;
use Exception;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Util\ThrowableToStringMapper;
use Throwable;

/**
 * Strings PHPUnit assertion exceptions.
 *
 * @see ExceptionPresenter
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class PHPUnitExceptionStringer implements ExceptionStringer
{
    public function supportsException(Exception $exception): bool
    {
        return $exception instanceof \PHPUnit\Framework\Exception;
    }

    public function stringException(Exception $exception, int $verbosity): string
    {
        try {
            if (class_exists(ThrowableToStringMapper::class)) {
                // PHPUnit 10.0.0 onwards
                return trim(ThrowableToStringMapper::map($exception));
            }

            if (class_exists(TestFailure::class)) {
                // PHPUnit 6.0.0 - 9.x
                /* @phpstan-ignore argument.type (PHPStan can't detect the interface of TestFailure::exceptionToString when a newer PHPUnit version is installed) */
                return trim(TestFailure::exceptionToString($exception));
            }

            // PHPUnit must be present, because we got a PHPUnit exception. So it must be a newer version with a
            // formatter class / method we don't know about.
            return sprintf(
                <<<TEXT
                %s
                !! Could not render more details of this %s.
                   Behat does not support automatically formatting assertion failures for your PHPUnit version.
                   See %s for details.
                TEXT,
                $exception->getMessage(),
                $exception::class,
                self::class,
            );
        } catch (Throwable $phpunitException) {
            // PHPUnit does not guarantee BC on the classes / methods we're calling.
            //
            // So it is likely that it looked like a version we expected, but the method signature or internal typing
            // has changed, causing an error at runtime.
            //
            // It is also possible that there's something in the user input (e.g. a value passed to $expect) that is
            // causing an error when PHPUnit tries to stringify it - but PHPUnit is generally quite robust about
            // catching those situations and reporting them within the message body.
            return sprintf(
                <<<TEXT
                %s
                !! There was an error trying to render more details of this %s.
                   You are probably using a PHPUnit version that Behat cannot automatically display failures for.
                   See %s for details of PHPUnit support.
                   [%s] %s at %s:%s
                TEXT,
                $exception->getMessage(),
                $exception::class,
                self::class,
                $phpunitException::class,
                $phpunitException->getMessage(),
                $phpunitException->getFile(),
                $phpunitException->getLine(),
            );
        }
    }
}
