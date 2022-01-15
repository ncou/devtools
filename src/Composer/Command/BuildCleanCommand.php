<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCleanCommand extends AbstractProcessCommand
{
    public function getBaseName(): string
    {
        return 'build:clean:all';
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return [
            $this->withPrefix('build:clean'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        return ['git', 'clean', '-fX', '.build/'];
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription(
                'Cleans the .build/ directory.',
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Cleaning the .build directory...</info>');

        $exitCode = parent::doExecute($input, $output);

        if ($exitCode !== 0) {
            $output->writeln('<error>Unable to clean the .build directory</error>');
        }

        return $exitCode;
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command will erase everything from the <info>.build/</info>
            directory that isn't committed to Git.

            You may use the <info>.build/</info> directory to store any artifacts your program
            produces that you do not wish to have under version control.

            By default, the <info>.build/</info> directory includes subdirectories for <info>cache/</info>
            and <info>coverage/</info> reports. Anything else you place here will be ignored by Git,
            unless you modify the <info>.gitignore</info> file.
            EOD;
    }
}
