<?php

declare(strict_types=1);

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use RuntimeException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Hook\AfterSuite;
use Behat\Hook\BeforeScenario;
use Behat\Hook\BeforeSuite;
use Behat\Step\Then;
use Behat\Step\When;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use tests\Behat\PHPUnitAssertionsExtension\JsonReportSummariser;

/**
 * Behat test suite context.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class FeatureContext implements Context
{
    private Process $process;

    private string $workingDir;

    private string $jsonReportFile;

    public function __construct(
        private readonly Filesystem $filesystem = new Filesystem(),
    ) {
    }

    /**
     * Cleans test folders in the temporary directory.
     */
    #[BeforeSuite]
    #[AfterSuite]
    public static function cleanTestFolders(): void
    {
        (new Filesystem())->remove(sys_get_temp_dir().DIRECTORY_SEPARATOR.'behat');
    }

    /**
     * Prepares test folders in the temporary directory.
     */
    #[BeforeScenario]
    public function prepareTestFolders(): void
    {
        $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'behat'.DIRECTORY_SEPARATOR.
            md5(microtime().random_int(0, 10000));

        $this->filesystem->mkdir($dir);
        $this->workingDir = $dir;
    }

    #[When('I initialise the working directory from the :dir fixtures folder')]
    public function iSetTheWorkingDirectoryToTheFixturesFolder(string $dir): void
    {
        $basePath = dirname(__DIR__, 2).'/tests/Fixtures/';
        $dir = $basePath.$dir;
        if (!is_dir($dir)) {
            throw new RuntimeException(sprintf('The directory "%s" does not exist', $dir));
        }
        $this->filesystem->mirror($dir, $this->workingDir);
    }

    #[When('/^I run Behat (?P<withOrWithout>with|without) this extension$/')]
    #[When('/^I run Behat (?P<withOrWithout>with|without) this extension and filtered to "(?P<tags>[^"]+)"$/')]
    public function runBehat(string $withOrWithout, string $tags = ''): void
    {
        $options = [];
        if ($withOrWithout === 'with') {
            $options[] = '--profile=with-extension';
        }
        if ($tags !== '') {
            $options[] = '--tags='.$tags;
        }
        $this->runBehatWithOptions($options);
    }

    /**
     * @param list<string> $arguments
     */
    private function runBehatWithOptions(array $arguments): void
    {
        $this->jsonReportFile = $this->workingDir.DIRECTORY_SEPARATOR.'behat-report.json';
        $command = [
            __DIR__.'/../../vendor/bin/behat',
            '--no-interaction',
            '--format=progress',
            '--out=std',
            '--format=json',
            '--out='.$this->jsonReportFile,
            ...$arguments,
        ];

        $this->process = new Process(
            $command,
            $this->workingDir,
        );

        $this->process->setTimeout(20);

        $this->process->run();
    }

    #[Then('/^it should (?P<result>fail|pass) with the following results:$/')]
    public function assertBehatResultMatches(string $passOrFail, PyStringNode $expectedReport): void
    {
        $expectedExit = match ($passOrFail) {
            'pass' => 0,
            'fail' => 1,
            default => throw new InvalidArgumentException(sprintf('Invalid passOrFail value: %s', $passOrFail)),
        };

        if ($this->getExitCode() !== $expectedExit) {
            throw new RuntimeException(
                sprintf(
                    <<<'TEXT'
                    Expected Behat to %s, but got exit code %d.
                    Output:
                    %s
                    TEXT,
                    $passOrFail,
                    $this->getExitCode(),
                    $this->getOutput(),
                ));
        }

        $this->assertStringsMatch(
            $expectedReport->getRaw(),
            $this->summariseJsonReport(),
            'Behat result did not match expectation',
        );
    }

    private function getExitCode(): ?int
    {
        return $this->process->getExitCode();
    }

    private function getOutput(): string
    {
        return $this->process->getErrorOutput().$this->process->getOutput();
    }

    private function diff(string $expected, string $actual): string
    {
        $differ = new Differ(new DiffOnlyOutputBuilder());

        return $differ->diff($expected, $actual);
    }

    private function assertStringsMatch(string $expected, string $actual, string $message): void
    {
        if ($expected === $actual) {
            return;
        }

        throw new UnexpectedValueException($message.PHP_EOL.PHP_EOL.$this->diff($expected, $actual));
    }

    private function summariseJsonReport(): string
    {
        if (!isset($this->jsonReportFile)) {
            throw new RuntimeException('This Scenario has not triggered Behat to write a JSON report file');
        }

        try {
            $reportJSON = $this->readFile($this->jsonReportFile);

            return (new JsonReportSummariser())->summarise($reportJSON);
        } catch (Throwable $e) {
            throw new RuntimeException(
                sprintf(
                    <<<'TEXT'
                    Failed to read JSON report file from %s:
                    [%s] %s

                    Behat output:
                    %s
                    TEXT,
                    $this->jsonReportFile,
                    $e::class,
                    $e->getMessage(),
                    $this->getOutput(),
                ),
                $e->getCode(),
                $e,
            );
        }
    }

    private function readFile(string $filePath): string
    {
        // Once we drop support for older symfony versions, we can use the `readFile` provided by
        // symfony/filesystem. Until then we need to implement our own.
        if (!is_file($filePath)) {
            throw new RuntimeException('No file at ' . $filePath);
        }

        set_error_handler(
            static fn (int $severity, string $message, string $file, int $line) => throw new ErrorException($message, 0, $severity, $file, $line)
        );

        try {
            $contents = file_get_contents($filePath);
            assert($contents !== false, 'file_get_contents() should not return false without emitting a PHP warning');

            return $contents;

        } finally {
            restore_error_handler();
        }
    }
}
