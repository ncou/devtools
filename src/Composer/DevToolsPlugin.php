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
use Composer\Util\Filesystem;

use function dirname;
use function realpath;

// TODO : utiliser un outil de license type : https://github.com/malukenho/docheader
// https://github.com/mezzio/mezzio/blob/3.9.x/composer.json#L106
// psalm --shepherd --stats

//https://github.com/viperproject/check-license-header

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

    private string $repoRoot; // TODO : renommer en $baseDir !!!

    public function __construct()
    {
        // TODO : virer la fonction __construct() et déplacer le code dans la méthode activate()

        // TODO : on peut soit faire un realpath('.') ou faire un getcwd() pour avoir
        // le répertoire racine ca sera plus propre que ce bout de code !!!!

        // TODO : utiliser la classe Composer : Platform::getCwd(true)   => https://github.com/composer/composer/blob/be4b70ce79b34762acf1647e63108fdcca7f758b/src/Composer/Factory.php#L168
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
        // TODO : je pense qu'il faut vérifier que le plugin est bien activé avant de
        //retourner le tableau des commandes. prendre exemple sur Symfony/flex et retourner
        //un tableau vide on n'a pas activé le plugin car sinon on aura surement une
        //exception car $composer ou $repoRoot n'aura pas été activé !!!!

        // https://github.com/symfony/flex/blob/1.x/src/Flex.php#L106
        // https://github.com/symfony/flex/blob/1.x/src/Flex.php#L291
        // https://github.com/symfony/flex/blob/1.x/src/Flex.php#L966

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
        self::$composer = $composer; // TODO : faire plutot un $this->composer = $composer;    https://github.com/symfony/flex/blob/1.x/src/Flex.php#L119

        // TODO : récupérer la commande (ex : update / create-project ...etc) pour ne créer les répertoires que dans certains cas.
        // Par exemple lors de la commande "du" ou "dump-update" pas la peine de créer les répertoires !!!!

        // https://github.com/symfony/flex/blob/2.x/src/Flex.php#L148
        // https://github.com/symfony/thanks/blob/main/src/Thanks.php#L49
        // https://github.com/narrowspark/automatic-composer-prefetcher/blob/master/Plugin.php#L375

        //$io->write('<info>Creating .build directory structure</info>');

        // TODO : utiliser le $this->repoRoot au lieu du "." en début de path pour la création des répertoires !!!!!
        // Creating build directory structure.
        $fs = new Filesystem();
        $fs->ensureDirectoryExists('./.build');
        $fs->ensureDirectoryExists('./.build/cache');
        $fs->ensureDirectoryExists('./.build/coverage');
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
