<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class LintCommand extends BaseCommand
{
    public function getBaseName(): string
    {
        return 'lint:all';
    }

    /**
     * Supports the use of `composer lint`, without the command prefix/namespace
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return ['lint'];
    }

    public function isProxyCommand(): bool
    {
        return true;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs all linting checks.')
            ->setHelp($this->getHelpText());
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $lintSyntax = $this->getApplication()->find($this->withPrefix('lint:syntax'));
        $lintStyle = $this->getApplication()->find($this->withPrefix('lint:style'));

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $lintSyntax->getName())]);
        $lintSyntaxExit = $lintSyntax->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $lintStyle->getName())]);
        $lintStyleExit = $lintStyle->run($input, $output);

        return $lintSyntaxExit + $lintStyleExit;
    }

    private function getHelpText(): string
    {
        $lintSyntax = $this->withPrefix('lint:syntax');
        $lintStyle = $this->withPrefix('lint:style');

        return <<<EOD
            <info>%command.name%</info> executes the <info>{$lintSyntax}</info> and <info>{$lintStyle}</info>
            commands.

            Since this command executes multiple commands, it is not possible
            to pass additional arguments to the commands. You may, however,
            extend or override these commands for your own needs. See the
            chiron/devtools README.md file for more information.
            EOD;
    }
}