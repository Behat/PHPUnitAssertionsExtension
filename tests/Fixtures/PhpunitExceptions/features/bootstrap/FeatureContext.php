<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use PHPUnit\Framework\Assert;

class FeatureContext implements Context
{
    #[Then('/^an array (?P<actual_json>.+?) should equal (?P<expected_json>.+)$/')]
    public function arrayShouldMatch(string $actual_json, string $expected_json): void
    {
        // To prove the output with more complex diffs
        Assert::assertEquals(
            json_decode($expected_json, true),
            json_decode($actual_json, true),
            'Should get the right value'
        );
    }

    #[Then('an integer :actual should equal :expected')]
    public function intShouldMatch(int $actual, int $expected): void
    {
        Assert::assertSame($expected, $actual, 'check the ints');
    }
}
