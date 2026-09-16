@phpunit-any
Feature: PHPUnit assertions are not shown with details by default
  In order to know that this extension is working
  As an extension developer
  I need to know that Behat is not automatically formatting PHPUnit exceptions

  Background:
    Given I initialise the working directory from the "PhpunitExceptions" fixtures folder

  Scenario: Basic exception failure output without context
    When  I run Behat without this extension
    Then  it should fail with the following results:
      """
      # FAILED Scenario: Compare mismatched array
      Then an array {"value": "foo"} should equal {"value": "bar"}: Should get the right value
      Failed asserting that two arrays are equal. (PHPUnit\Framework\ExpectationFailedException)

      # PASSED Scenario: Compare matching array
      (no failures)

      # FAILED Scenario: Compare mismatched ints
      Then an integer 1 should equal 2: check the ints
      Failed asserting that 1 is identical to 2. (PHPUnit\Framework\ExpectationFailedException)

      # PASSED Scenario: Compare matching ints
      (no failures)
      """

  Scenario: Works as expected when all scenarios pass
    When  I run Behat without this extension and filtered to "@passing"
    Then  it should pass with the following results:
      """
      # PASSED Scenario: Compare matching array
      (no failures)

      # PASSED Scenario: Compare matching ints
      (no failures)
      """
