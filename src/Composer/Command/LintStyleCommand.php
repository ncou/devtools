<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class LintStyleCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'lint:style';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        if ($input->getOption('phpcs-help')) {
            // Ignore all other arguments and display phpcs help.
            $args = ['--help'];
        }

        return array_merge(
            [
                $this->withBinPath('phpcs'),
                '--colors',
                '--cache=build/cache/phpcs.cache',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Checks for coding standards issues.')
            ->addUsage('--phpcs-help')
            ->addUsage('-- [<phpcs-options>...]')
            ->addUsage('-- [<file>...]')
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
                new InputOption('phpcs-help', null, InputOption::VALUE_NONE, 'Display phpcs help'),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>phpcs</info> (PHP_CodeSniffer).
            It uses any local configuration files (e.g., phpcs.xml) available.

            For more information on phpcs, see
            https://github.com/squizlabs/PHP_CodeSniffer

            You may also pass additional arguments to phpcs. To do so, use a
            double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to phpcs.

            For example:

              <info>%command.full_name% -- src/File1.php src/File2.php</info>

            To view phpcs help, use the <info>--phpcs-help</info> option.

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to phpcs. These include standard
            options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these options,
            invoke phpcs directly via <info>./vendor/bin/phpcs</info>.
            EOD;
    }
}
