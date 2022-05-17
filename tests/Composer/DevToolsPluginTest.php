<?php

declare(strict_types=1);

namespace Chiron\Test\Dev\Tools\Composer;

use Chiron\Dev\Tools\Composer\Command\AbstractBaseCommand;
use Chiron\Dev\Tools\Composer\DevToolsPlugin;
use Chiron\Dev\Tools\TestSuite\AbstractTestCase;
use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Mockery;
use Mockery\MockInterface;

use function count;

class DevToolsPluginTest extends AbstractTestCase
{
    public function testGetCapabilities(): void
    {
        $plugin = new DevToolsPlugin();

        $this->assertSame(
            [
                CommandProvider::class => DevToolsPlugin::class,
            ],
            $plugin->getCapabilities(),
        );
    }

    public function testGetCommands(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);
        $config->allows()->get('bin-dir')->andReturn('/path/to/bin-dir');

        /** @var EventDispatcher & MockInterface $eventDispatcher */
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->shouldReceive('addListener');
        $eventDispatcher->shouldReceive('dispatchScript')->andReturn(0);

        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [
                'command-prefix' => 'foo',
            ],
            'getConfig'            => $config,
            'getEventDispatcher'   => $eventDispatcher,
        ]);

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        $pluginActivated = new DevToolsPlugin();
        $pluginActivated->activate($composer, $io);

        // This will test that our $composer instance was set
        // statically on the class.
        $pluginToUseForCommands = new DevToolsPlugin();
        $commands = $pluginToUseForCommands->getCommands();

        $this->assertContainsOnlyInstancesOf(AbstractBaseCommand::class, $commands);
        $this->assertGreaterThan(0, count($commands));
        $this->assertSame('foo:', $commands[0]->getPrefix());
        $this->assertSame('/path/to/bin-dir', $commands[0]->getBinDir());
    }

    public function testGetCommandsWithChironDevtoolsCommandPrefixProperty(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);
        $config->allows()->get('bin-dir')->andReturn('/path/to/bin-dir');

        /** @var EventDispatcher & MockInterface $eventDispatcher */
        $eventDispatcher = $this->mockery(EventDispatcher::class);
        $eventDispatcher->shouldReceive('addListener');
        $eventDispatcher->shouldReceive('dispatchScript')->andReturn(0);

        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [
                'command-prefix'  => 'foo',
                'chiron/devtools' => [
                    'command-prefix' => 'bar',
                ],
            ],
            'getConfig'            => $config,
            'getEventDispatcher'   => $eventDispatcher,
        ]);

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        $pluginActivated = new DevToolsPlugin();
        $pluginActivated->activate($composer, $io);

        // This will test that our $composer instance was set
        // statically on the class.
        $pluginToUseForCommands = new DevToolsPlugin();
        $commands = $pluginToUseForCommands->getCommands();

        $this->assertContainsOnlyInstancesOf(AbstractBaseCommand::class, $commands);
        $this->assertGreaterThan(0, count($commands));
        $this->assertSame('bar:', $commands[0]->getPrefix());
        $this->assertSame('/path/to/bin-dir', $commands[0]->getBinDir());
    }

    public function testActivate(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = Mockery::mock(Composer::class);

        /** @var IOInterface & MockInterface $io */
        $io = Mockery::spy(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->activate($composer, $io);

        $composer->shouldNotHaveBeenCalled();
        $io->shouldNotHaveBeenCalled();
    }

    public function testDeactivate(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = Mockery::mock(Composer::class);

        /** @var IOInterface & MockInterface $io */
        $io = Mockery::spy(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->deactivate($composer, $io);

        $composer->shouldNotHaveBeenCalled();
        $io->shouldNotHaveBeenCalled();
    }

    public function testUninstall(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = Mockery::mock(Composer::class);

        /** @var IOInterface & MockInterface $io */
        $io = Mockery::spy(IOInterface::class);

        $plugin = new DevToolsPlugin();
        $plugin->uninstall($composer, $io);

        $composer->shouldNotHaveBeenCalled();
        $io->shouldNotHaveBeenCalled();
    }
}
