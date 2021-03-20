<?php
/**
 * CodeframeTest
 */
declare(strict_types=1);

namespace Kregel\ExceptionProbe\Tests;

use Kregel\ExceptionProbe\Codeframe;
use PHPUnit\Framework\TestCase;

/**
 * Class CodeframeTest
 * @package Kregel\ExceptionProbe\Tests
 */
class CodeframeTest extends TestCase
{
    public function testWeCanGetTheFile()
    {
        $codeframe = new Codeframe('file', 10, ['line'], 'frame');
        self::assertSame('file', $codeframe->getFile());
    }

    public function testWeCanSetTheFile()
    {
        $codeframe = new Codeframe('file', 10, ['line'], 'frame');
        $codeframe->setFile('TestFile');
        self::assertNotEquals('file', $codeframe->getFile());
        self::assertEquals('TestFile', $codeframe->getFile());
    }
}
