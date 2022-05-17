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
     * Composer v2.2.3 use an old Symfony process package (v2.8.52) expecting the command to be a string.
     * The newest Process package expect an array. This method detect the type (array or string) to use for the command.
     * Starting v3.3 you can use both string|array and in version 4.2, Process constructor need an array an the function fromShellCommandline has been introduced
     *
     * @param string[] $command
     *
     * @return string[]|string
     *
     * @throws ReflectionException
     */
    protected function useCorrectCommand(array $command): array|string
    {
        /** // TODO : ce code semble plus simple à utiliser (ca correspond à une version Process 4.2) :
    https://github.com/composer/composer/blob/2.2/src/Composer/Command/InitCommand.php#L749 */

        $reflectedProcess = new ReflectionClass($this->getProcessClassName());

        /** @var ReflectionMethod $reflectedConstructor */
        $reflectedConstructor = $reflectedProcess->getConstructor();
        $reflectedConstructorType = $reflectedConstructor->getParameters()[0]->getType();

        if ($reflectedConstructorType instanceof ReflectionNamedType) {
            if ($reflectedConstructorType->getName() === 'array') {
                return $command;
            }
        }

        // TODO : je pense qu'on peux utiliser un Composer\Util\ProcessExecutor::escape() au lieu de escapeshellarg
        $commandLine = array_shift($command) . ' ';
        $commandLine .= implode(' ', array_map(fn ($v) => escapeshellarg($v), $command)); // TODO virer le fn qui ne sert à rien: implode(' ', array_map('escapeshellarg', $command));

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
