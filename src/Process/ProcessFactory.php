<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Process;

use ReflectionException;

/**
 * Factory to create a Process instance for running commands
 *
 * @internal
 */
class ProcessFactory
{
    /**
     * @param string[] $command
     *
     * @throws ReflectionException
     */
    public function factory(array $command, ?string $cwd = null): Process
    {
        return new Process($command, $cwd);
    }
}