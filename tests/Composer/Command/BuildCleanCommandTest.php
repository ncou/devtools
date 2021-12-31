<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\Composer\Command\BuildCleanCommand;

class BuildCleanCommandTest extends ProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = BuildCleanCommand::class;
        $this->baseName = 'build:clean:all';
        $this->processCommand = ['git', 'clean', '-fX', 'build/'];

        parent::setUp();
    }

    public function testGetAliases(): void
    {
        $this->assertSame(['bar:build:clean'], $this->command->getAliases());
    }

    public function testRun(): void
    {
        $this->output->expects()->writeln('<info>Cleaning the build directory...</info>');

        parent::testRun();
    }

    public function testRunWithFailure(): void
    {
        $this->output->expects()->writeln('<info>Cleaning the build directory...</info>');
        $this->output->expects()->writeln('<error>Unable to clean the build directory</error>');

        $this->doTestRun(
            function (callable $callback): int {
                $callback('', 'test buffer string');

                return 127;
            },
            127,
        );
    }
}
