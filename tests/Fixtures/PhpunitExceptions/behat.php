<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\TesterOptions;
use Behat\PHPUnitAssertionsExtension\BehatPHPUnitAssertionsExtension;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withTesterOptions(
                (new TesterOptions())->withPrintBehatDeprecations())
            )

    ->withProfile(
        (new Profile('with-extension'))
            ->withExtension(new Extension(BehatPHPUnitAssertionsExtension::class)),
    );
