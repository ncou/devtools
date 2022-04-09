<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer;

use Chiron\Dev\Tools\Process\ProcessFactory;
use Composer\Composer;

/**
 * Configuration for commands
 */
// TODO : reporter cette classe un cran plus haut dans la hierarchie des répertoires, elle doit être à la base src\Composer\ et pas dans le rep Command car ce n'est pas une commande !!!!
class Configuration
{
    private Composer $composer;
    private string $commandPrefix;
    private string $repositoryRoot;
    private ProcessFactory $processFactory;

    public function __construct(
        Composer $composer,
        string $commandPrefix,
        string $repositoryRoot,
        ?ProcessFactory $processFactory = null
    ) {
        $this->composer = $composer;
        $this->commandPrefix = $commandPrefix;
        $this->repositoryRoot = $repositoryRoot;
        $this->processFactory = $processFactory ?? new ProcessFactory();
    }

    public function getComposer(): Composer
    {
        return $this->composer;
    }

    public function getCommandPrefix(): string
    {
        return $this->commandPrefix;
    }

    public function getRepositoryRoot(): string
    {
        return $this->repositoryRoot;
    }

    public function getProcessFactory(): ProcessFactory
    {
        return $this->processFactory;
    }
}
