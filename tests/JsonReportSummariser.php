<?php

namespace tests\Behat\PHPUnitAssertionsExtension;

/**
 * @phpstan-type TBehatJsonReport array{suites: list<TBehatJsonSuite>, failures?: list<TBehatJsonFailureEntry>}
 * @phpstan-type TBehatJsonSuite array{name: ?string, features: list<TBehatJsonFeature>, failures?: list<TBehatJsonFailureEntry>}
 * @phpstan-type TBehatJsonFeature array{name: ?string, scenarios: list<TBehatJsonScenario>, failures?: list<TBehatJsonFailureEntry>}
 * @phpstan-type TBehatJsonScenario array{name: ?string, status: string, failures?: list<TBehatJsonFailureEntry>}
 * @phpstan-type TBehatJsonFailureEntry array{type:string, message:string}
 */
final class JsonReportSummariser
{
    public function summarise(string $jsonReport): string
    {
        $report = $this->parseBehatJsonReport($jsonReport);
        $actual = [];
        foreach ($report['suites'] as $suite) {
            foreach ($suite['features'] as $feature) {
                foreach ($feature['scenarios'] as $scenario) {
                    $actual[] = $this->summariseScenarioResult($scenario);
                }

                if ($feature['failures'] ?? []) {
                    $actual[] = sprintf(
                        "# FEATURE FAILURES: %s\n%s",
                        $feature['name'],
                        $this->summariseFailures($feature['failures'])
                    );
                }
            }

            if ($suite['failures'] ?? []) {
                $actual[] = sprintf(
                    "# SUITE FAILURES:%s\n%s",
                    $suite['name'],
                    $this->summariseFailures($suite['failures'])
                );
            }
        }

        return implode("\n\n", $actual);
    }

    /**
     * @phpstan-return TBehatJsonReport
     */
    private function parseBehatJsonReport(string $jsonReport): array
    {
        /* @phpstan-ignore return.type (Trust that our JSON matches the expected Behat schema) */
        return json_decode($jsonReport, associative: true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @phpstan-param TBehatJsonScenario $scenario
     */
    private function summariseScenarioResult(array $scenario): string
    {
        $header = sprintf(
            '# %s Scenario: %s',
            strtoupper($scenario['status']),
            $scenario['name'],
        );

        return $header."\n".$this->summariseFailures($scenario['failures'] ?? []);
    }

    /**
     * @phpstan-param list<TBehatJsonFailureEntry> $failures
     */
    private function summariseFailures(array $failures): string
    {
        if ($failures === []) {
            return '(no failures)';
        }

        return implode("\n", array_column($failures, 'message'));
    }
}
