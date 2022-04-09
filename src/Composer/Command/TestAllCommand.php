<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

// TODO : utiliser un bout de code pour executer les commandes "composer run lint:all" par exemple. Ca éviterai de se trainer la méthode getApplication !!!!
//https://github.com/veewee/composer-run-parallel/blob/e776154c2910cdc0b3b4400196fd2aa0b3da7251/src/Executor/AsyncTaskExecutor.php#L45

// TODO : il faut modifier le Input qui est passé aux fonctions appellées pour faire suivre le verbose/quiet par exemple.
//https://github.com/laravel/framework/blob/b9203fca96960ef9cd8860cb4ec99d1279353a8d/src/Illuminate/Console/Concerns/CallsCommands.php#L78

class TestAllCommand extends AbstractBaseCommand
{
    public function getBaseName(): string
    {
        return 'test:all';
    }

    /**
     * Supports the use of `composer test`, without the command prefix/namespace
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return ['test'];
    }

    public function isProxyCommand(): bool
    {
        return true;
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription('Runs linting, static analysis, and unit tests.');
    }

    /**
     * @throws Exception
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $lint = $this->getApplication()->find($this->withPrefix('lint:all'));
        $analyze = $this->getApplication()->find($this->withPrefix('analyze:all'));
        $test = $this->getApplication()->find($this->withPrefix('test:unit'));

        $lintExit = $lint->run($input, $output);
        $analyzeExit = $analyze->run($input, $output);

        $output->writeln(['', sprintf('<comment>Executing %s</comment>', (string) $test->getName())]);
        $testExit = $test->run($input, $output);

        return $lintExit + $analyzeExit + $testExit;
    }

    private function getHelpText(): string
    {
        $lintAll = $this->withPrefix('lint:all');
        $analyzeAll = $this->withPrefix('analyze:all');
        $testUnit = $this->withPrefix('test:unit');

        return <<<EOD
            <info>%command.name%</info> executes the <info>{$lintAll}</info>, <info>{$analyzeAll}</info>,
            and <info>{$testUnit}</info> commands.

            Since this command executes multiple commands, it is not possible
            to pass additional arguments to the commands. You may, however,
            extend or override these commands for your own needs. See the
            chiron/devtools README.md file for more information.
            EOD;
    }
}
