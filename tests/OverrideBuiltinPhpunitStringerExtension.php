<?php

namespace tests\Behat\PHPUnitAssertionsExtension;

use Behat\Testwork\Deprecation\DeprecationCollector;
use Behat\Testwork\Exception\ServiceContainer\ExceptionExtension;
use Behat\Testwork\Exception\Stringer\PHPUnitExceptionStringer;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use UnexpectedValueException;

class OverrideBuiltinPhpunitStringerExtension implements Extension
{
    public function getConfigKey(): string
    {
        return 'phpunit_assertions_hack';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
        // no-op
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        // no-op
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        // While we are installing Behat 4.0-alpha1, the distributed Behat package already provides the old
        // built-in PHPUnitExceptionStringer. We need to explicitly remove that from the container so that
        // we can prove it is our own extension that is formatting the output.
        //
        // We will be able to remove this once we have a Behat 4.x release that doesn't include the old stringer
        if (!class_exists(PHPUnitExceptionStringer::class)) {
            DeprecationCollector::trigger(
                'The installed Behat version no longer provides a built-in phpunit stringer, this extension can be removed',
            );
        }

        $builtinServiceTag = ExceptionExtension::STRINGER_TAG.'.phpunit';
        if (!$container->hasDefinition($builtinServiceTag)) {
            throw new UnexpectedValueException(
                'The installed Behat version did not install a phpunit stringer under '.$builtinServiceTag,
            );
        }

        $container->removeDefinition(ExceptionExtension::STRINGER_TAG.'.phpunit');
    }

    public function process(ContainerBuilder $container): void
    {
        // no-op
    }
}
