<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\TesterOptions;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withTesterOptions((new TesterOptions())
                ->withFailOnBehatDeprecations(),
            ),
    );
