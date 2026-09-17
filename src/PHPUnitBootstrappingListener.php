<?php

namespace Behat\PHPUnitAssertionsExtension;

use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use PHPUnit\TextUI\Configuration\Builder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PHPUnitBootstrappingListener implements EventSubscriberInterface
{
    private bool $isBootstrapped = false;

    public static function getSubscribedEvents(): array
    {
        return [
            // Ensure this runs before other listeners or hooks that might make assertions
            SuiteTested::BEFORE => ['bootstrapPHPUnit', -100],
        ];
    }

    public function bootstrapPHPUnit(): void
    {
        if ($this->isBootstrapped) {
            return;
        }

        if (class_exists(Builder::class)) {
            // Bootstrap PHPUnit 11+ - must be done before any call to Assert::
            (new Builder())->build([]);
        }

        $this->isBootstrapped = true;
    }
}
