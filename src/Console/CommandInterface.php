<?php

declare(strict_types=1);

namespace Serixa\Console;

/**
 * Executable CLI command contract.
 */
interface CommandInterface
{
    public function name(): string;

    public function description(): string;

    /**
     * @param list<string> $arguments Positional args after the command name
     * @param array<string, string|bool> $options Parsed --flags
     */
    public function handle(array $arguments, array $options, InputOutput $io): int;
}
