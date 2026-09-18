Feature: PHPUnit assertions are not shown with details by default
  In order to know that this extension is working
  As an extension developer
  I need to know that Behat is not automatically formatting PHPUnit exceptions

  Background:
    Given I initialise the working directory from the "PhpunitExceptions" fixtures folder

  @phpunit-8 @phpunit-9 @phpunit-10
  Scenario: Basic exception failure output without context when assertions on PHPUnit <= 10
      With earlier versions, all assertions produce an exception with a basic exception message,
      but not the detailed failure description or diff.

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

  @phpunit-11 @phpunit-12 @phpunit-13
  Scenario: Assertions may fail or lose details (without this extension) on PHPUnit 11+
      PHPUnit 11+ has to be bootstrapped before it can even build the failure message for some assertions,
      so without this extension users will see partial details or an assertion failure from within PHPUnit.

    When  I run Behat without this extension
    Then  it should fail with the following results:
      """
      # FAILED Scenario: Compare mismatched array
      Then an array {"value": "foo"} should equal {"value": "bar"}: Should get the right value
      Failed asserting that two arrays are equal. (PHPUnit\Framework\ExpectationFailedException)

      # PASSED Scenario: Compare matching array
      (no failures)

      # FAILED Scenario: Compare mismatched ints
      Then an integer 1 should equal 2: Fatal error: assert(self::$instance instanceof Configuration) (Behat\Testwork\Call\Exception\FatalThrowableError)

      # PASSED Scenario: Compare matching ints
      (no failures)
      """

  @phpunit-any
  Scenario: Works as expected when all scenarios pass
    When  I run Behat without this extension and filtered to "@passing"
    Then  it should pass with the following results:
      """
      # PASSED Scenario: Compare matching array
      (no failures)

      # PASSED Scenario: Compare matching ints
      (no failures)
      """
