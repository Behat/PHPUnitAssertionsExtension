<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\GherkinOptions;
use Behat\Config\Profile;
use Behat\Config\TesterOptions;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withTesterOptions((new TesterOptions())
                ->withFailOnBehatDeprecations(),
            ),
    )
    ->withProfile(
        (new Profile('phpunit-8'))
            ->withGherkinOptions((new GherkinOptions())
                ->withFilter(new TagFilter('@phpunit-any, @phpunit-8'))
            )
    )->withProfile(
        (new Profile('phpunit-9'))
            ->withGherkinOptions((new GherkinOptions())
                ->withFilter(new TagFilter('@phpunit-any, @phpunit-9'))
            )
    )->withProfile(
        (new Profile('phpunit-10'))
            ->withGherkinOptions((new GherkinOptions())
                ->withFilter(new TagFilter('@phpunit-any, @phpunit-10'))
            )
    )->withProfile(
        (new Profile('phpunit-11'))
            ->withGherkinOptions((new GherkinOptions())
                ->withFilter(new TagFilter('@phpunit-any, @phpunit-11'))
            )
    )->withProfile(
        (new Profile('phpunit-12'))
            ->withGherkinOptions((new GherkinOptions())
                ->withFilter(new TagFilter('@phpunit-any, @phpunit-12'))
            )
    )->withProfile(
        (new Profile('phpunit-13'))
            ->withGherkinOptions((new GherkinOptions())
                ->withFilter(new TagFilter('@phpunit-any, @phpunit-13'))
            )
    );
