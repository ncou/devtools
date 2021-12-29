<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;

class LicenseCheckerCommand extends ProcessCommand
{
    public function getBaseName(): string
    {
        return 'license';
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
                $this->withBinPath('license-checker'),
                '--ansi',
            ],
            $args,
        );
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getHelpText())
            ->setDescription('Checks dependency licenses.')
            ->setDefinition([
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ]);
    }

    private function getHelpText(): string
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return <<<'EOD'
            The <info>%command.name%</info> command executes <info>license-checker</info> from
            the madewithlove/license-checker package.

            To get started with license-checker, generate a config file based on
            the licenses your project currently uses:

              <info>%command.full_name% -- generate-config</info>

            Now, check the licenses of Composer dependencies to see if they match
            those your project allows:

              <info>%command.full_name% -- check</info>

            For more information on License Checker, see
            https://github.com/madewithlove/license-checker-php

            You may also pass arguments and options to license-checker. To do so,
            use a double-dash (<info>--</info>) to indicate all following arguments and options
            should be passed along directly to license-checker.

            For example:

              <info>%command.full_name% -- used</info>
              <info>%command.full_name% -- help generate-config</info>

            To view all license-checker sub-commands, use the <info>list</info> command:

              <info>%command.full_name% -- list</info>

            <comment>Please Note:</comment> Composer captures some options early and, therefore,
            cannot easily pass them along to license-checker. These include
            standard options such as <info>--help</info>, <info>--version</info>, and <info>--quiet</info>. To use these
            options, invoke license-checker directly via
            <info>./vendor/bin/license-checker</info>.
            EOD;
    }
}