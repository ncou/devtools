<?php

declare(strict_types=1);

namespace Chiron\Tests\Dev\Tools\Composer\Command;

use Chiron\Dev\Tools\AbstractTestCase;
use Chiron\Dev\Tools\Composer\Command\Configuration;
use Chiron\Dev\Tools\Process\ProcessFactory;
use Composer\Composer;
use Mockery\MockInterface;

class ConfigurationTest extends AbstractTestCase
{
    public function testConfiguration(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class);
        $commandPrefix = 'foo';
        $repositoryRoot = '/path/to/repo';

        $config = new Configuration($composer, $commandPrefix, $repositoryRoot);

        $this->assertSame($composer, $config->getComposer());
        $this->assertSame($commandPrefix, $config->getCommandPrefix());
        $this->assertSame($repositoryRoot, $config->getRepositoryRoot());
        $this->assertInstanceOf(ProcessFactory::class, $config->getProcessFactory());
    }
}
