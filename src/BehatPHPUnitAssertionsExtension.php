<?php

namespace Behat\PHPUnitAssertionsExtension;

use Behat\Testwork\Exception\ServiceContainer\ExceptionExtension;
use Behat\Testwork\Exception\Stringer\PHPUnitExceptionStringer;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class BehatPHPUnitAssertionsExtension implements Extension
{
    public function getConfigKey(): string
    {
        return 'phpunit_assertions';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
        // No-op
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        // No-op
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $definition = new Definition(PHPUnitExceptionStringer::class);
        $definition->addTag(ExceptionExtension::STRINGER_TAG, ['priority' => 50]);
        $container->setDefinition(ExceptionExtension::STRINGER_TAG. '.phpunit_assertions', $definition);
    }

    public function process(ContainerBuilder $container): void
    {
        // No-op
    }
}
