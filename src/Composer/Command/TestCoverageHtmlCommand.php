<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class TestCoverageHtmlCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'test:coverage:html';
    }

    /**
     * @inheritDoc
     */
    public function getProcessCommand(InputInterface $input, OutputInterface $output): array
    {
        /** @var string[] $args */
        $args = $input->getArguments()['args'] ?? [];

        return array_merge(
            [
                $this->withBinPath('phpunit'),
                '--colors=always',
                '--coverage-html',
                'build/coverage/coverage-html',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription('Runs unit tests and generates HTML coverage report.')
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>phpunit</info>, generating
            a coverage report in HTML format. It uses any local configuration
            files (e.g., phpunit.xml) available.

            The HTML coverage report is saved to <info>build/coverage/coverage-html/</info>.

            For more information on phpunit, see https://phpunit.de

            You may extend or override this command for your own needs. See the
            chiron/devtools README.md file for more information.
            EOD;
    }
}