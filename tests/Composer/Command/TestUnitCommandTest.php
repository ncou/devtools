<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\Composer\Command\TestUnitCommand;
use Symfony\Component\Console\Input\InputInterface;

class TestUnitCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = TestUnitCommand::class;
        $this->baseName = 'test:unit';
        $this->processCommand = ['/path/to/bin-dir/phpunit', '--colors=always', '--group', 'foo'];

        parent::setUp();

        $this->input->allows()->getOption('phpunit-help')->andReturnFalse();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--group', 'foo'],
        ]);
    }

    public function testWithPhpunitHelpOption(): void
    {
        $this->input = $this->mockery(InputInterface::class);
        $this->input->allows()->getOption('phpunit-help')->andReturnTrue();
        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar'],
        ]);

        $this->processCommand = ['/path/to/bin-dir/phpunit', '--colors=always', '--help'];

        $this->testRun();
    }
}
