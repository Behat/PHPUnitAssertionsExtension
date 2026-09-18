@phpunit-any
Feature: PHPUnit assertions extension works as expected when scenarios pass
  In order to have a healthy build
  As a feature developer using PHPUnit assertions
  I need a passing build to execute without problems when I enable this extension

  Scenario: Works as expected when all scenarios pass
    Given I initialise the working directory from the "PhpunitExceptions" fixtures folder
    When  I run Behat with this extension and filtered to "@passing"
    Then  it should pass with the following results:
      """
      # PASSED Scenario: Compare matching array
      (no failures)

      # PASSED Scenario: Compare matching ints
      (no failures)
      """
