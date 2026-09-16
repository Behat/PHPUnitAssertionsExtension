<?php

use Behat\Behat\Context\Context;
use Behat\Step\Given;

class FeatureContext implements Context
{
    #[Given('the build passes')]
    public function theBuildPasses(): void
    {
        // Great, it does
    }
}
