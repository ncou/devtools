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
     * @param ?string  $cwd
     *
     * @throws ReflectionException
     *
     * @return Process
     */
    public function factory(array $command, ?string $cwd = null): Process
    {
        $process = new Process($command, $cwd); // TODO : utiliser plutot le paramétre (timeout: null) du constructeur de la classe Process pour désactiver le timeout !!!!
        // Init the timeout to 300 seconds (=5mn).
        $process->setTimeout(300);

        return $process;
    }
}
