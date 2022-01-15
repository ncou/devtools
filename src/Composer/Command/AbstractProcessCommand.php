<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** TODO : transformer cette classe en Trait ??? */
abstract class AbstractProcessCommand extends AbstractBaseCommand
{
    /**
     * @return string[]
     */
    abstract public function getProcessCommand(InputInterface $input, OutputInterface $output): array;

    protected function getProcessCallback(OutputInterface $output): callable
    {
        return function (string $_type, string $buffer) use ($output): void {
            $output->write($buffer);
        };
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $process = $this->getConfiguration()->getProcessFactory()->factory(
            command: $this->getProcessCommand($input, $output),
            cwd: $this->getConfiguration()->getRepositoryRoot(),
        );

        $process->start();

        return $process->wait($this->getProcessCallback($output));
    }
}
