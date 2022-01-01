<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\Composer\Command\LicenseCheckerCommand;

class LicenseCheckerCommandTest extends AbstractProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = LicenseCheckerCommand::class;
        $this->baseName = 'license';
        $this->processCommand = ['/path/to/bin-dir/license-checker', '--ansi', '--foo'];

        parent::setUp();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--foo'],
        ]);
    }
}
