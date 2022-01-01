<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer;

use Chiron\Dev\Tools\Composer\Command\AbstractBaseCommand;
use Chiron\Dev\Tools\Composer\Command\AnalyzeCommand;
use Chiron\Dev\Tools\Composer\Command\AnalyzePhpStanCommand;
use Chiron\Dev\Tools\Composer\Command\AnalyzePsalmCommand;
use Chiron\Dev\Tools\Composer\Command\BuildCleanCacheCommand;
use Chiron\Dev\Tools\Composer\Command\BuildCleanCommand;
use Chiron\Dev\Tools\Composer\Command\BuildCleanCoverageCommand;
use Chiron\Dev\Tools\Composer\Command\Configuration;
use Chiron\Dev\Tools\Composer\Command\LicenseCheckerCommand;
use Chiron\Dev\Tools\Composer\Command\LintCommand;
use Chiron\Dev\Tools\Composer\Command\LintFixCommand;
use Chiron\Dev\Tools\Composer\Command\LintStyleCommand;
use Chiron\Dev\Tools\Composer\Command\LintSyntaxCommand;
use Chiron\Dev\Tools\Composer\Command\TestAllCommand;
use Chiron\Dev\Tools\Composer\Command\TestCoverageCiCommand;
use Chiron\Dev\Tools\Composer\Command\TestCoverageHtmlCommand;
use Chiron\Dev\Tools\Composer\Command\TestUnitCommand;
use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

use function dirname;
use function realpath;

/**
 * Provides a variety of Composer commands and events useful for PHP
 * library and application development
 */
class DevToolsPlugin implements
    Capable,
    CommandProvider,
    PluginInterface
{
    private static Composer $composer;

    private string $repoRoot;

    public function __construct()
    {
        $composerFile = Factory::getComposerFile();

        $this->repoRoot = (string) realpath(dirname($composerFile));
    }

    /**
     * @return array<string, string>
     */
    public function getCapabilities(): array
    {
        return [
            CommandProvider::class => self::class,
        ];
    }

    /**
     * @return AbstractBaseCommand[]
     */
    public function getCommands(): array
    {
        $config = new Configuration(self::$composer, $this->getCommandPrefix(), $this->repoRoot);

        return [
            new AnalyzeCommand($config),
            new AnalyzePhpStanCommand($config),
            new AnalyzePsalmCommand($config),
            new BuildCleanCacheCommand($config),
            new BuildCleanCommand($config),
            new BuildCleanCoverageCommand($config),
            new LicenseCheckerCommand($config),
            new LintCommand($config),
            new LintFixCommand($config),
            new LintStyleCommand($config),
            new LintSyntaxCommand($config),
            new TestAllCommand($config),
            new TestCoverageCiCommand($config),
            new TestCoverageHtmlCommand($config),
            new TestUnitCommand($config),
        ];
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        self::$composer = $composer;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * Use extra.command-prefix, if available, but extra.chiron/devtools.command-prefix
     * takes precedence over extra.command-prefix.
     */
    private function getCommandPrefix(): string
    {
        /** @var array{command-prefix?: string, "chiron/devtools"?: array{command-prefix?: string}} $extra */
        $extra = self::$composer->getPackage()->getExtra();

        return $extra['chiron/devtools']['command-prefix'] ?? $extra['command-prefix'] ?? '';
    }
}
