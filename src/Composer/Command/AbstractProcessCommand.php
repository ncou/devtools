<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// TODO : utiliser la classe ProcessExecutor pour lancer les lignes de commandes !!!!!
//https://github.com/composer/composer/blob/main/src/Composer/Util/ProcessExecutor.php
//https://github.com/symfony/flex/blob/375e01daedd481501c29f3dea443cf885858382f/src/ScriptExecutor.php#L58
//https://github.com/symfony/flex/blob/97634769241adf2cbfde0cec2c336c2612c06d49/src/Update/RecipePatcher.php#L164
//https://github.com/symfony/flex/blob/97634769241adf2cbfde0cec2c336c2612c06d49/src/Command/UpdateRecipesCommand.php#L415

// TODO : utiliser ce bout de code (commandStringToArgs) pour convertir la
// command sous forme de string vers un tableau :
//https://github.com/cakephp/cakephp/blob/5.x/src/TestSuite/ConsoleIntegrationTestTrait.php#L272

// Autre exemple : https://github.com/mnapoli/silly/blob/master/src/Command/ExpressionParser.php#L13


/** TODO : transformer cette classe en Trait ??? */
abstract class AbstractProcessCommand extends AbstractBaseCommand
{
    /**
     * @return string[]
     */
    abstract public function getProcessCommand(InputInterface $input, OutputInterface $output): array;

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $process = $this->getConfiguration()->getProcessFactory()->factory(
            command: $this->getProcessCommand($input, $output),
            cwd: $this->getConfiguration()->getRepositoryRoot(),
        );

        // TODO : faire plutot un ProcessExecutor->execute() avec une commande sous forme de string, et passer le timeout de composer à 0/null.
        $process->start();

        return $process->wait($this->getProcessCallback($output));
    }

    /** https://github.com/composer/composer/blob/49867f08750e603d2861b2b9a56f22401e0c3495/src/Composer/Util/ProcessExecutor.php#L212 */
    protected function getProcessCallback(OutputInterface $output): callable
    {
        /** @psalm-suppress UnusedClosureParam */
        return function (string $type, string $buffer) use ($output): void {
            $output->write($buffer, false, OutputInterface::OUTPUT_RAW);
        };
    }
}
