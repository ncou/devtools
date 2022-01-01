<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\Composer\Command\LintFixCommand;
use Symfony\Component\Console\Input\InputInterface;

class LintFixCommandTest extends AbstractProcessCommandTestCase
{
    protected function setUp(): void
    {
        $this->commandClass = LintFixCommand::class;
        $this->baseName = 'lint:fix';
        $this->processCommand = ['/path/to/bin-dir/phpcbf', '--cache=build/cache/phpcs.cache', '--qux'];

        parent::setUp();

        $this->input->allows()->getOption('phpcbf-help')->andReturnFalse();

        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--qux'],
        ]);
    }

    public function testWithPhpcbfHelpOption(): void
    {
        $this->input = $this->mockery(InputInterface::class);
        $this->input->allows()->getOption('phpcbf-help')->andReturnTrue();
        $this->input->allows()->getArguments()->andReturn([
            'args' => ['--bar'],
        ]);

        $this->processCommand = ['/path/to/bin-dir/phpcbf', '--cache=build/cache/phpcs.cache', '--help'];

        $this->testRun();
    }

    public function testRunSucceedsWithExitCode1(): void
    {
        $this->doTestRun(
            function (callable $callback): int {
                $callback('', 'test buffer string');

                return 1;
            },
            0,
        );
    }

    public function testRunFailsOver1(): void
    {
        $this->doTestRun(
            function (callable $callback): int {
                $callback('', 'test buffer string');

                return 2;
            },
            2,
        );
    }
}
