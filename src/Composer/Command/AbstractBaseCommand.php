<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Composer\Command\BaseCommand as ComposerBaseCommand;
use Composer\EventDispatcher\EventDispatcher;
use RuntimeException;
//use Symfony\Component\Console\Application;
use Composer\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chiron\Dev\Tools\Composer\Configuration;

use function assert;
use function is_string;
use function substr;

use const DIRECTORY_SEPARATOR;

/**
 * @psalm-consistent-constructor
 */
abstract class AbstractBaseCommand extends ComposerBaseCommand
{
    private Configuration $configuration;
    private string $binDir;
    private EventDispatcher $eventDispatcher;
    private bool $overrideDefault;

    /**
     * Returns the name of this command, without the command prefix
     */
    abstract public function getBaseName(): string;

    /**
     * Called by the execute() command in this AbstractBaseCommand class
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output): int;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $binDir = $configuration->getComposer()->getConfig()->get('bin-dir');
        assert(is_string($binDir));
        $this->binDir = $binDir;

        $this->eventDispatcher = $configuration->getComposer()->getEventDispatcher();
        $this->setComposer($configuration->getComposer());

        parent::__construct($this->withPrefix($this->getBaseName()));

        $extra = $configuration->getComposer()->getPackage()->getExtra();

        /** @var array{command-prefix?: string, commands?: array<string, mixed>} $devtoolsConfig */
        $devtoolsConfig = $extra['chiron/devtools'] ?? [];

        /** @var array{override?: bool, script?: array<string>|string} $commandConfig */
        $commandConfig = $devtoolsConfig['commands'][$this->getBaseName()] ?? [];

        $this->overrideDefault = $commandConfig['override'] ?? false;

        $additionalScripts = (array) ($commandConfig['script'] ?? []);

        /** @var callable $script */
        foreach ($additionalScripts as $script) {
            $this->eventDispatcher->addListener((string) $this->getName(), $script);
        }
    }

    // TODO : je ne sais pas si le final à un sens ici, à confirmer !!!
    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        if (! $this->overrideDefault) {
            $exitCode = $this->doExecute($input, $output);
        }

        return $exitCode + $this->eventDispatcher->dispatchScript((string) $this->getName());
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getBinDir(): string
    {
        return $this->binDir;
    }

    public function withBinPath(string $bin): string
    {
        $path = $this->getBinDir() . DIRECTORY_SEPARATOR . $bin;

        return str_replace('\\', '/', $path);
    }

    public function getPrefix(): string
    {
        $prefix = $this->configuration->getCommandPrefix();

        if ($prefix !== '' && substr($prefix, -1) !== ':') {
            $prefix .= ':';
        }

        return $prefix;
    }

    public function withPrefix(string $name): string
    {
        return $this->getPrefix() . $name;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    // TODO : vérifier l'utilité de cette fonction car on a déjà la methode qui existe dans la commande de base, à quoi sert cette surcharge pour seulement faire un throw dans un cas trés rare qui ne devrait pas arriver !!!! Et dans la derniére version de Composer il y a déjà ce contrôle !!!!
    //https://github.com/composer/composer/blob/f125fc1d0ac1e050fd11acb48f72bd3aae060dba/src/Composer/Command/BaseCommand.php#L55
    /*
    public function getApplication(): Application
    {
        // @var Application|null $application
        $application = parent::getApplication();

        if ($application === null) {
            throw new RuntimeException('Could not find an Application instance');
        }

        return $application;
    }*/
}
