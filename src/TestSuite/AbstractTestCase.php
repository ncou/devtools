<?php

declare(strict_types=1);

namespace Chiron\Dev\Tools\TestSuite;

use Chiron\Dev\Tools\TestSuite\Traits\MockeryHelperTrait;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

// TODO : ajouter un helper pour la reflection d'une classe et accéder à une méthode ou propriété privée (et changer sa valeur par exemple) :
//https://github.com/windwalker-io/test/blob/master/src/Traits/TestAccessorTrait.php
//https://github.com/windwalker-io/utilities/blob/master/src/Reflection/ReflectAccessor.php

//TODO : regarder les fonctions utiles ici (assertTextNotContains, assertTextEndsWith, assertFileDoesNotExist, skipIf ...etc)
//https://github.com/cakephp/cakephp/blob/5.x/src/TestSuite/TestCase.php
//https://github.com/cakephp/cakephp/blob/5.x/src/TestSuite/StringCompareTrait.php
// TODO : vérifier quand même que ces nouveaux assert ne sont pas des doublons de ce qui existe déjà dans PHPUNIT : https://github.com/sebastianbergmann/phpunit/blob/master/src/Framework/Assert.php

// TODO : assertion de base à récupérer !!! https://github.com/windwalker-io/test/blob/master/src/Traits/BaseAssertionTrait.php

// TODO : prendre exemple sur ce trait qui permet d'utiliser une classe d'utils pour lire les propriétés d'une classe !!!
// https://github.com/windwalker-io/test/blob/master/src/Traits/TestAccessorTrait.php
//https://github.com/windwalker-io/utilities/blob/master/src/Reflection/ReflectAccessor.php#L186

//https://github.com/windwalker-io/test/blob/master/src/Traits/BaseAssertionTrait.php

//https://github.com/jasny/phpunit-extension/blob/master/src/PrivateAccessTrait.php
//https://github.com/lstrojny/phpunit-clever-and-smart/blob/master/src/PHPUnit/Runner/CleverAndSmart/Util.php#L35

//https://github.com/diablomedia/phpunit-pretty-printer
//https://github.com/mnapoli/phpunit-easymock

// TODO : transformer cette classe en trait ???
/**
 * A base test case for common test functionality
 */
abstract class AbstractTestCase extends PHPUnitTestCase
{
    use MockeryHelperTrait;
}
