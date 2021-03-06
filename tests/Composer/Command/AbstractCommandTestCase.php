<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\Composer\Command\AbstractBaseCommand;
use Chiron\Dev\Tools\Composer\Configuration;
use Chiron\Dev\Tools\Process\ProcessFactory;
use Chiron\Dev\Tools\TestSuite\AbstractTestCase;
use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Mockery\MockInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

abstract class AbstractCommandTestCase extends AbstractTestCase
{
    /**
     * @var class-string
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected string $commandClass;

    protected ?string $baseName = null;
    protected string $prefix = 'bar';
    protected string $binDir = '/path/to/bin-dir';
    protected string $repositoryRoot = '/path/to/repo';

    protected AbstractBaseCommand $command;

    /**
     * @var Composer & MockInterface
     */
    protected Composer $composer;

    /**
     * @var Config & MockInterface
     */
    protected Config $config;

    /**
     * @var ProcessFactory & MockInterface
     */
    protected ProcessFactory $processFactory;

    /**
     * @var EventDispatcher & MockInterface
     */
    protected EventDispatcher $eventDispatcher;

    abstract public function testRun(): void;

    protected function setUp(): void
    {
        $commandClass = $this->commandClass;

        $this->config = $this->mockery(Config::class);
        $this->config->allows()->get('bin-dir')->andReturn($this->binDir);

        $this->eventDispatcher = $this->mockery(EventDispatcher::class);
        $this->eventDispatcher->shouldReceive('dispatch');
        $this->eventDispatcher->shouldReceive('dispatchScript')->andReturn(0);

        $this->composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [],
            'getConfig'            => $this->config,
            'getEventDispatcher'   => $this->eventDispatcher,
        ]);

        $this->processFactory = $this->mockery(ProcessFactory::class);

        $configuration = new Configuration(
            $this->composer,
            $this->prefix,
            $this->repositoryRoot,
            $this->processFactory,
        );

        /**
         * @psalm-suppress MixedMethodCall
         *
         * @var AbstractBaseCommand $command
         */
        $command = new $commandClass($configuration);

        $this->command = $command;
    }

    public function testGetBaseName(): void
    {
        $this->assertSame($this->baseName, $this->command->getBaseName());
    }

    public function testGetBinDir(): void
    {
        $this->assertSame($this->binDir, $this->command->getBinDir());
    }

    public function testWithBinPath(): void
    {
        $binWithPath = $this->binDir . '/' . 'foo';

        $this->assertSame($binWithPath, $this->command->withBinPath('foo'));
    }

    public function testGetPrefix(): void
    {
        $expectedPrefix = $this->prefix ? $this->prefix . ':' : '';

        $this->assertSame($expectedPrefix, $this->command->getPrefix());
    }

    public function testWithPrefix(): void
    {
        $expected = $this->prefix ? $this->prefix . ':cmd' : 'cmd';

        $this->assertSame($expected, $this->command->withPrefix('cmd'));
    }

    public function testRunWithOverride(): void
    {
        $this->composer->shouldReceive('getPackage->getExtra')->andReturn([
            'chiron/devtools' => [
                'commands' => [
                    $this->command->getBaseName() => [
                        'override' => true,
                    ],
                ],
            ],
        ]);

        $input = new StringInput('');
        $output = new NullOutput();

        $configuration = new Configuration(
            $this->composer,
            $this->prefix,
            $this->repositoryRoot,
            $this->processFactory,
        );

        /** @var class-string<AbstractBaseCommand> $commandClass */
        $commandClass = $this->commandClass;
        $command = new $commandClass($configuration);

        $this->assertSame(0, $command->run($input, $output));
    }

    public function testRunWithAdditionalScripts(): void
    {
        $this->composer->shouldReceive('getPackage->getExtra')->andReturn([
            'chiron/devtools' => [
                'commands' => [
                    $this->command->getBaseName() => [
                        'script' => [
                            'a script to run',
                            'another script to run',
                        ],
                    ],
                ],
            ],
        ]);

        $this->eventDispatcher->expects()->addListener(
            $this->command->getName(),
            'a script to run',
        );

        $this->eventDispatcher->expects()->addListener(
            $this->command->getName(),
            'another script to run',
        );

        /** @var class-string<AbstractBaseCommand> $commandClass */
        $commandClass = $this->commandClass;

        $configuration = new Configuration(
            $this->composer,
            $this->prefix,
            $this->repositoryRoot,
            $this->processFactory,
        );

        // Replace the command with a new one, since the constructor is where
        // it figures out if there are additional scripts to add listeners for.
        $this->command = new $commandClass($configuration);

        $this->testRun();
    }
}
