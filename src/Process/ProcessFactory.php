<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Process;

use Symfony\Component\Process\Process;

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
     * @return Process
     */
    public function factory(array $command, ?string $cwd = null): Process
    {
        return new Process($command, $cwd);
    }
}
