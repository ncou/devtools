<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\Composer\Command\TestCoverageHtmlCommand;

class TestCoverageHtmlCommandTest extends AbstractProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = TestCoverageHtmlCommand::class;
        $this->baseName = 'test:coverage:html';
        $this->processCommand = [
            '/path/to/bin-dir/phpunit',
            '--colors=always',
            '--coverage-html',
            'build/coverage/coverage-html',
            '--group',
            'bip',
        ];

        parent::setUp();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--group', 'bip'],
        ]);
    }
}
