<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\PHPUnitAssertionsExtension\BehatPHPUnitAssertionsExtension;
use tests\Behat\PHPUnitAssertionsExtension\OverrideBuiltinPhpunitStringerExtension;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withExtension(new Extension(OverrideBuiltinPhpunitStringerExtension::class)),
    )
    ->withProfile(
        (new Profile('with-extension'))
            ->withExtension(new Extension(BehatPHPUnitAssertionsExtension::class)),
    );
