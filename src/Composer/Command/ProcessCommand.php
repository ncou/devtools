<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ProcessCommand extends BaseCommand
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

    /**
     * @throws ReflectionException
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $process = $this->getConfiguration()->getProcessFactory()->factory(
            $this->getProcessCommand($input, $output),
            $this->getConfiguration()->getRepositoryRoot(),
        );

        $process->start();

        return $process->wait($this->getProcessCallback($output));
    }
}
