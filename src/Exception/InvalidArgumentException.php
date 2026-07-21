<?php

declare(strict_types=1);

namespace Serixa\Exception;

/**
 * Invalid argument or configuration supplied to a SERIXA API.
 *
 * Extends SPL InvalidArgumentException so existing catch blocks keep working.
 */
class InvalidArgumentException extends \InvalidArgumentException
{
}
