<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagExpressionFilter;
use Behat\Config\GherkinOptions;
use Behat\Config\Profile;
use Behat\Config\TesterOptions;
use Behat\Gherkin\GherkinCompatibilityMode;
use Composer\InstalledVersions;

$behatVersion = InstalledVersions::getVersion('behat/behat');
$behatFilter = version_compare($behatVersion, '4.0', '>=')
    ? 'not @behat-3'
    : 'not @behat-4';

$config = (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withGherkinOptions((new GherkinOptions())
                ->withCompatibilityMode(GherkinCompatibilityMode::GHERKIN_42)
            )
            ->withTesterOptions((new TesterOptions())
                ->withFailOnBehatDeprecations(),
            ),
    );

foreach ([8, 9, 10, 11, 12, 13] as $phpunitVersion) {
    $config = $config->withProfile(
        (new Profile('phpunit-'.$phpunitVersion))
            ->withGherkinOptions((new GherkinOptions())
                ->withFilter(new TagExpressionFilter(
                    sprintf('(@phpunit-any or @phpunit-%d) and %s', $phpunitVersion, $behatFilter)
                ))
            )
    );
}

return $config;
