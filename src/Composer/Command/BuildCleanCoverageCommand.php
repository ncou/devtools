<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCleanCoverageCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'build:clean:coverage';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        return ['git', 'clean', '-fX', 'build/coverage/.'];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription(
                'Cleans the build/coverage/ directory.',
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Clearing the coverage reports...</info>');

        $exitCode = parent::doExecute($input, $output);

        if ($exitCode !== 0) {
            $output->writeln('<error>Unable to clear the coverage reports</error>');
        }

        return $exitCode;
    }

    private function getHelpText(): string
    {
        $buildClean = $this->withPrefix('build:clean');

        return <<<EOD
            The <info>%command.name%</info> command will erase everything from the
            <info>build/coverage/</info> directory that isn't committed to Git.

            This is helpful to clean up cached HTML or XML files from coverage
            reports.

            This command erases only the contents of <info>build/coverage/</info>, while
            <info>{$buildClean}</info> erases everything else from the <info>build/</info> directory. If you
            wish to keep other build artifacts and erase only the coverage,
            <info>%command.name%</info> is the command to use.
            EOD;
    }
}
