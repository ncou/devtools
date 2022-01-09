<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Process;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\Process\Process as SymfonyProcess;

use function array_map;
use function array_shift;
use function escapeshellarg;
use function implode;

/**
 * @internal
 */
class Process extends SymfonyProcess
{
    /**
     * @param string[] $command
     *
     * @throws ReflectionException
     *
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function __construct(array $command, ?string $cwd = null)
    {
        // @phpstan-ignore-next-line
        parent::__construct($this->useCorrectCommand($command), $cwd);
    }

    /**
     * @param string[] $command
     *
     * @return string[]|string
     *
     * @throws ReflectionException
     */
    protected function useCorrectCommand(array $command)
    {
        $reflectedProcess = new ReflectionClass($this->getProcessClassName());

        /** @var ReflectionMethod $reflectedConstructor */
        $reflectedConstructor = $reflectedProcess->getConstructor();
        $reflectedConstructorType = $reflectedConstructor->getParameters()[0]->getType();

        if ($reflectedConstructorType instanceof ReflectionNamedType) {
            if ($reflectedConstructorType->getName() === 'array') {
                return $command;
            }
        }

        $commandLine = array_shift($command) . ' ';
        $commandLine .= implode(' ', array_map(fn ($v) => escapeshellarg($v), $command));

        return $commandLine;
    }

    /**
     * For internal test-mocking purposes only
     *
     * @return class-string
     */
    protected function getProcessClassName(): string
    {
        return SymfonyProcess::class;
    }
}
