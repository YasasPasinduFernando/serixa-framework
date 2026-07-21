<?php

declare(strict_types=1);

namespace Serixa\Console;

use Serixa\Exception\InvalidArgumentException;

/**
 * Discovers and stores CLI commands (built-ins + plugin registrations).
 */
final class CommandRegistry
{
    private static ?self $default = null;

    /** @var array<string, CommandInterface> */
    private array $commands = [];

    public static function default(): self
    {
        return self::$default ??= new self();
    }

    public static function resetDefault(): void
    {
        self::$default = null;
    }

    public function register(CommandInterface $command): void
    {
        $name = $command->name();

        if ($name === '') {
            throw new InvalidArgumentException('Command name cannot be empty.');
        }

        if (isset($this->commands[$name])) {
            throw new InvalidArgumentException(sprintf('Command "%s" is already registered.', $name));
        }

        $this->commands[$name] = $command;
    }

    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    public function get(string $name): ?CommandInterface
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * @return array<string, CommandInterface>
     */
    public function all(): array
    {
        ksort($this->commands);

        return $this->commands;
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        $names = array_keys($this->commands);
        sort($names);

        return $names;
    }
}
