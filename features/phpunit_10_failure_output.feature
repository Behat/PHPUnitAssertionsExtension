@phpunit-10
Feature: PHPUnit assertions are rendered as expected with PHPUnit 10
  In order to understand why my features failed
  As a feature developer using PHPUnit 10
  I need to see the details of any assertion failures in the test report

  Scenario: Formatted assertion output when assertions fail
    Given I initialise the working directory from the "PhpunitExceptions" fixtures folder
    When  I run Behat with this extension
    Then  it should fail with the following results:
      """
      # FAILED Scenario: Compare mismatched array
      Then an array {"value": "foo"} should equal {"value": "bar"}: Should get the right value
      Failed asserting that two arrays are equal.
      --- Expected
      +++ Actual
      @@ @@
       Array (
      -    'value' => 'bar'
      +    'value' => 'foo'
       )

      # PASSED Scenario: Compare matching array
      (no failures)

      # FAILED Scenario: Compare mismatched ints
      Then an integer 1 should equal 2: check the ints
      Failed asserting that 1 is identical to 2.

      # PASSED Scenario: Compare matching ints
      (no failures)
      """
