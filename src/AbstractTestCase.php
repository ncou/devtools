<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools;

use Hamcrest\Util;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

//https://github.com/jasny/phpunit-extension/blob/master/src/PrivateAccessTrait.php
//https://github.com/lstrojny/phpunit-clever-and-smart/blob/master/src/PHPUnit/Runner/CleverAndSmart/Util.php#L35

//https://github.com/diablomedia/phpunit-pretty-printer
//https://github.com/mnapoli/phpunit-easymock

/**
 * A base test case for common test functionality
 */
abstract class AbstractTestCase extends PHPUnitTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @codeCoverageIgnore
     */
    public static function setUpBeforeClass(): void
    {
        Util::registerGlobalFunctions();
    }

    /**
     * Configures and returns a mock object
     *
     * @param class-string<T> $class
     * @param mixed           ...$arguments
     *
     * @return T & MockInterface
     *
     * @template T
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function mockery(string $class, ...$arguments)
    {
        /** @var T & MockInterface $mock */
        $mock = Mockery::mock($class, ...$arguments);

        return $mock;
    }
}
