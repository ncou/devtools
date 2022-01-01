<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\Composer\Command\AnalyzePsalmCommand;
use Symfony\Component\Console\Input\InputInterface;

class AnalyzePsalmCommandTest extends AbstractProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = AnalyzePsalmCommand::class;
        $this->baseName = 'analyze:psalm';
        $this->processCommand = [
            '/path/to/bin-dir/psalm',
            '--bar',
            '--baz',
        ];

        parent::setUp();

        $this->input->allows()->getOption('psalm-help')->andReturnFalse();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar', '--baz'],
        ]);
    }

    public function testWithPsalmHelpOption(): void
    {
        $this->input = $this->mockery(InputInterface::class);
        $this->input->allows()->getOption('psalm-help')->andReturnTrue();
        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar'],
        ]);

        $this->processCommand = ['/path/to/bin-dir/psalm', '--help'];

        $this->testRun();
    }
}
