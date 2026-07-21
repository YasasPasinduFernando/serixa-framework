<?php

declare(strict_types=1);

namespace Serixa\Console;

/**
 * Thin IO adapter for CLI commands (testable).
 */
final class InputOutput
{
    /** @var list<string> */
    private array $lines = [];

    public function __construct(
        private readonly bool $capture = false,
    ) {
    }

    public function writeln(string $message = ''): void
    {
        if ($this->capture) {
            $this->lines[] = $message;

            return;
        }

        echo $message, PHP_EOL;
    }

    public function info(string $message): void
    {
        $this->writeln($message);
    }

    public function success(string $message): void
    {
        $this->writeln($message);
    }

    public function error(string $message): void
    {
        if ($this->capture) {
            $this->lines[] = $message;

            return;
        }

        fwrite(STDERR, $message . PHP_EOL);
    }

    /**
     * @return list<string>
     */
    public function lines(): array
    {
        return $this->lines;
    }

    public function output(): string
    {
        return implode(PHP_EOL, $this->lines);
    }
}
