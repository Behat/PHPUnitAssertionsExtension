<?php

namespace Behat\PHPUnitAssertionsExtension;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BehatPHPUnitAssertionsExtension implements Extension
{
    public function getConfigKey(): string
    {
        return 'phpunit_assertions';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
        // TODO: Implement initialize() method.
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        // TODO: Implement configure() method.
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        // TODO: Implement load() method.
    }

    public function process(ContainerBuilder $container): void
    {
        // TODO: Implement process() method.
    }
}
