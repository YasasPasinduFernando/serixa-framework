<?php

declare(strict_types=1);

namespace Serixa\Console;

/**
 * Abstract CLI command with shared naming helpers.
 */
abstract class Command implements CommandInterface
{
    abstract public function name(): string;

    abstract public function description(): string;

    /**
     * @param list<string> $arguments
     * @param array<string, string|bool> $options
     */
    abstract public function handle(array $arguments, array $options, InputOutput $io): int;

    /**
     * @param list<string> $arguments
     */
    protected function requireArgument(array $arguments, int $index, string $label, InputOutput $io): ?string
    {
        if (! isset($arguments[$index]) || $arguments[$index] === '') {
            $io->error(sprintf('Missing required argument <%s>.', $label));

            return null;
        }

        return $arguments[$index];
    }
}
