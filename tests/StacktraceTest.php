<?php
/**
 * StacktraceTest
 */
declare(strict_types=1);

namespace Kregel\ExceptionProbe\Tests;

use Kregel\ExceptionProbe\Stacktrace;
use PHPUnit\Framework\TestCase;

/**
 * Class StacktraceTest
 * @package Kregel\ExceptionProbe\Tests
 */
class StacktraceTest extends TestCase
{
    /**
     * @var Stacktrace
     */
    protected $stacktrace;

    public function setUp()
    {
        $this->stacktrace = new Stacktrace();
    }

    public function testWeCanParseFromAnException()
    {
        try {
            (new TestClassException())->handle();
        } catch (\Exception $exception) {
            $array = $this->stacktrace->parse($exception->getTraceAsString());
            $this->assertTrue(is_array($array));
        }
    }

    public function testWeGetAnInvalidFile()
    {
        $exceptionString = '#0 /some/fake/file.php(31): Kregel\ExceptionProbe\Tests\TestClassException->handle()';
        $array = $this->stacktrace->parse($exceptionString);
        $this->assertTrue(is_array($array));
    }

    public function testWeGetAnUnWritableFile()
    {
        $exceptionString = '#0 ' . __DIR__ .'/empty-file-that-is-not-readable.php(31): Kregel\ExceptionProbe\Tests\TestClassException->handle()';
        $array = $this->stacktrace->parse($exceptionString);
        $this->assertTrue(is_array($array));
    }
}
