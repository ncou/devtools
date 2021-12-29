<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class TestCoverageCiCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'test:coverage:ci';
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
                '--coverage-text',
                '--coverage-clover',
                'build/coverage/clover.xml',
                '--coverage-cobertura',
                'build/coverage/cobertura.xml',
                '--coverage-crap4j',
                'build/coverage/crap4j.xml',
                '--coverage-xml',
                'build/coverage/coverage-xml',
                '--log-junit',
                'build/junit.xml',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription('Runs unit tests and generates CI coverage reports.')
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ]);
    }

    private function getHelpText(): string
    {
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>phpunit</info>, generating
            coverage reports and test logging suitable for CI (continuous
            integration) workflows. It uses any local configuration files
            (e.g., phpunit.xml) available.

            Coverage reports include:

              * Clover XML, saved to <info>build/coverage/clover.xml</info>
              * Cobertura XML, saved to <info>build/coverage/cobertura.xml</info>
              * Crap4J XML, saved to <info>build/coverage/crap4j.xml</info>
              * PHPUnit XML, saved to <info>build/coverage/coverage-xml/</info>

            Test logs include:

              * JUnit XML, saved to <info>build/junit.xml</info>

            For more information on phpunit, see https://phpunit.de

            You may extend or override this command for your own needs. See the
            chiron/devtools README.md file for more information.
            EOD;
    }
}