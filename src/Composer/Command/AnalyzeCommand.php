<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class AnalyzeCommand extends BaseCommand
{
    public function getBaseName(): string
    {
        return 'analyze:all';
    }

    /**
     * Supports the use of `composer analyze`, without the command prefix/namespace
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return ['analyze'];
    }

    public function isProxyCommand(): bool
    {
        return true;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs all static analysis checks.')
            ->setHelp($this->getHelpText());
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $phpStan = $this->getApplication()->find($this->withPrefix('analyze:phpstan'));
        $psalm = $this->getApplication()->find($this->withPrefix('analyze:psalm'));

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $phpStan->getName())]);
        $phpStanExit = $phpStan->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $psalm->getName())]);
        $psalmExit = $psalm->run($input, $output);

        return $phpStanExit + $psalmExit;
    }

    private function getHelpText(): string
    {
        $phpstanCommand = $this->withPrefix('analyze:phpstan');
        $psalmCommand = $this->withPrefix('analyze:psalm');

        return <<<EOD
            <info>%command.name%</info> executes both the <info>{$phpstanCommand}</info>
            and <info>{$psalmCommand}</info> commands.

            Since this command executes multiple commands, it is not possible
            to pass additional arguments to the commands. You may, however,
            extend or override these commands for your own needs. See the
            chiron/devtools README.md file for more information.
            EOD;
    }
}
