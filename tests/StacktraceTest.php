<?php
/**
 * StacktraceTest
 */
declare(strict_types=1);

namespace Kregel\ExceptionProbe\Tests;

use Kregel\ExceptionProbe\Codeframe;
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

    public function setUp(): void
    {
        $this->stacktrace = new Stacktrace();
    }

    public function testWeCanParseFromAnException()
    {
        try {
            (new TestClassException())->handle();
        } catch (\Exception $exception) {
            $array = $this->stacktrace->parse($exception->getTraceAsString());
            self::assertTrue(is_array($array));

            $firstCodeframe = $array[0];
            self::assertInstanceOf(Codeframe::class, $firstCodeframe);
            self::assertTrue(stripos($firstCodeframe->file, 'tests/StacktraceTests.php') !== true);
            self::assertSame('Kregel\ExceptionProbe\Tests\TestClassException->handle()', $firstCodeframe->frame);

            $codeLine = $firstCodeframe->code[32];
            self::assertSame('            (new TestClassException())->handle();' . "\n", $codeLine);

            $secondsCodeframe = $array[1];
            self::assertInstanceOf(Codeframe::class, $secondsCodeframe);
            self::assertTrue(stripos($secondsCodeframe->file, 'vendor/phpunit/phpunit/src/Framework/TestCase.php') !== true);
            self::assertSame('Kregel\ExceptionProbe\Tests\StacktraceTest->testWeCanParseFromAnException()', $secondsCodeframe->frame);

            $lastFrame = $array[8];
            self::assertInstanceOf(Codeframe::class, $lastFrame);
            self::assertTrue(stripos($lastFrame->file, 'vendor/phpunit/phpunit/src/TextUI/Command.php') !== true);
            self::assertSame('PHPUnit\TextUI\TestRunner->doRun()', $lastFrame->frame);
        }
    }

    public function testWeGetAnInvalidFile()
    {
        $exceptionString = '#0 /some/fake/file.php(31): Kregel\ExceptionProbe\Tests\TestClassException->handle()';
        $array = $this->stacktrace->parse($exceptionString);
        self::assertTrue(is_array($array));
        self::assertTrue(!empty($array));

        $firstCodeframe = $array[0];
        self::assertInstanceOf(Codeframe::class, $firstCodeframe);
        self::assertSame('/some/fake/file.php', $firstCodeframe->file);
        self::assertSame('Kregel\ExceptionProbe\Tests\TestClassException->handle()', $firstCodeframe->frame);
        self::assertCount(0, $firstCodeframe->code);
    }

    public function testWeGetAnUnWritableFile()
    {
        $exceptionString = '#0 ' . __DIR__ .'/empty-file-that-is-not-readable.php(31): Kregel\ExceptionProbe\Tests\TestClassException->handle()';
        $array = $this->stacktrace->parse($exceptionString);
        self::assertTrue(is_array($array));
    }

    public function testWeThrowAnException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $exceptionString = '';
        $array = $this->stacktrace->parse($exceptionString);
        self::assertTrue(is_array($array));
    }

    public function testWeCanHandleLaravelBasedExceptions()
    {
        $exceptionString = "Symfony\Component\Debug\Exception\FatalThrowableError: Too few arguments to function App\Domain\Service\GitHub::__construct(), 1 passed in /home/austinkregel/Sites/lager/app/Jobs/RefreshGithubData.php on line 44 and exactly 2 expected in /home/austinkregel/Sites/lager/app/Domain/Service/GitHub.php:29\nStack trace:\n#0 /home/austinkregel/Sites/lager/app/Jobs/RefreshGithubData.php(44): App\Domain\Service\GitHub->__construct(Object(App\Social))\n#1 [internal function]: App\Jobs\RefreshGithubData->handle()\n#2 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(29): call_user_func_array(Array, Array)\n#3 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(87): Illuminate\Container\BoundMethod::Illuminate\Container\{closure}()\n#4 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(31): Illuminate\Container\BoundMethod::callBoundMethod(Object(Illuminate\Foundation\Application), Array, Object(Closure))\n#5 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/Container.php(572): Illuminate\Container\BoundMethod::call(Object(Illuminate\Foundation\Application), Array, Array, NULL)\n#6 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(94): Illuminate\Container\Container->call(Array)\n#7 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(116): Illuminate\Bus\Dispatcher->Illuminate\Bus\{closure}(Object(App\Jobs\RefreshGithubData))\n#8 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(104): Illuminate\Pipeline\Pipeline->Illuminate\Pipeline\{closure}(Object(App\Jobs\RefreshGithubData))\n#9 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(98): Illuminate\Pipeline\Pipeline->then(Object(Closure))\n#10 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(49): Illuminate\Bus\Dispatcher->dispatchNow(Object(App\Jobs\RefreshGithubData), false)\n#11 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(83): Illuminate\Queue\CallQueuedHandler->call(Object(Illuminate\Queue\Jobs\RedisJob), Array)\n#12 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(327): Illuminate\Queue\Jobs\Job->fire()\n#13 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(277): Illuminate\Queue\Worker->process('redis', Object(Illuminate\Queue\Jobs\RedisJob), Object(Illuminate\Queue\WorkerOptions))\n#14 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(118): Illuminate\Queue\Worker->runJob(Object(Illuminate\Queue\Jobs\RedisJob), 'redis', Object(Illuminate\Queue\WorkerOptions))\n#15 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(102): Illuminate\Queue\Worker->daemon('redis', 'changelager', Object(Illuminate\Queue\WorkerOptions))\n#16 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(86): Illuminate\Queue\Console\WorkCommand->runWorker('redis', 'changelager')\n#17 /home/austinkregel/Sites/lager/vendor/laravel/horizon/src/Console/WorkCommand.php(46): Illuminate\Queue\Console\WorkCommand->handle()\n#18 [internal function]: Laravel\Horizon\Console\WorkCommand->handle()\n#19 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(29): call_user_func_array(Array, Array)\n#20 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(87): Illuminate\Container\BoundMethod::Illuminate\Container\{closure}()\n#21 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(31): Illuminate\Container\BoundMethod::callBoundMethod(Object(Illuminate\Foundation\Application), Array, Object(Closure))\n#22 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Container/Container.php(572): Illuminate\Container\BoundMethod::call(Object(Illuminate\Foundation\Application), Array, Array, NULL)\n#23 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Console/Command.php(183): Illuminate\Container\Container->call(Array)\n#24 /home/austinkregel/Sites/lager/vendor/symfony/console/Command/Command.php(255): Illuminate\Console\Command->execute(Object(Symfony\Component\Console\Input\ArgvInput), Object(Illuminate\Console\OutputStyle))\n#25 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Console/Command.php(170): Symfony\Component\Console\Command\Command->run(Object(Symfony\Component\Console\Input\ArgvInput), Object(Illuminate\Console\OutputStyle))\n#26 /home/austinkregel/Sites/lager/vendor/symfony/console/Application.php(893): Illuminate\Console\Command->run(Object(Symfony\Component\Console\Input\ArgvInput), Object(Symfony\Component\Console\Output\ConsoleOutput))\n#27 /home/austinkregel/Sites/lager/vendor/symfony/console/Application.php(262): Symfony\Component\Console\Application->doRunCommand(Object(Laravel\Horizon\Console\WorkCommand), Object(Symfony\Component\Console\Input\ArgvInput), Object(Symfony\Component\Console\Output\ConsoleOutput))\n#28 /home/austinkregel/Sites/lager/vendor/symfony/console/Application.php(145): Symfony\Component\Console\Application->doRun(Object(Symfony\Component\Console\Input\ArgvInput), Object(Symfony\Component\Console\Output\ConsoleOutput))\n#29 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Console/Application.php(89): Symfony\Component\Console\Application->run(Object(Symfony\Component\Console\Input\ArgvInput), Object(Symfony\Component\Console\Output\ConsoleOutput))\n#30 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(122): Illuminate\Console\Application->run(Object(Symfony\Component\Console\Input\ArgvInput), Object(Symfony\Component\Console\Output\ConsoleOutput))\n#31 /home/austinkregel/Sites/lager/artisan(37): Illuminate\Foundation\Console\Kernel->handle(Object(Symfony\Component\Console\Input\ArgvInput), Object(Symfony\Component\Console\Output\ConsoleOutput))\n#32 {main}";
        $array = $this->stacktrace->parse($exceptionString);
        self::assertTrue(is_array($array));
        self::assertTrue(!empty($array));

        $firstCodeframe = $array[0];
        self::assertInstanceOf(Codeframe::class, $firstCodeframe);
        self::assertSame('/home/austinkregel/Sites/lager/app/Jobs/RefreshGithubData.php', $firstCodeframe->file);
        self::assertSame('App\Domain\Service\GitHub->__construct(Object(App\Social))', $firstCodeframe->frame);
        self::assertCount(0, $firstCodeframe->code);
    }

    public function testWeCanHandlePDOBasedExceptions()
    {
        $exceptionString = "PDOException: SQLSTATE[22008]: Datetime field overflow: 7 ERROR:  date/time field value out of range: \"1967-12-0\"\nHINT:  Perhaps you need a different \"datestyle\" setting. in /home/austinkregel/Sites/lager/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOStatement.php:142\nStack trace:\n#0 /home/austinkregel/Sites/lager/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOStatement.php(142): PDOStatement->execute(NULL)\n#1 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Database/Connection.php(330): Doctrine\DBAL\Driver\PDOStatement->execute()\n#2 /home/austinkregel/Sites/lager/vendor/laravel/framework/src/Illuminate/Database/Connection.php(657): Illuminate\Database\Connection->Illuminate\Database\{closure}('insert into \"ta...', Array)";

        $array = $this->stacktrace->parse($exceptionString);
        self::assertTrue(is_array($array));
        self::assertTrue(!empty($array));

        $firstCodeframe = $array[0];
        self::assertInstanceOf(Codeframe::class, $firstCodeframe);
        self::assertSame('/home/austinkregel/Sites/lager/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOStatement.php', $firstCodeframe->file);
        self::assertSame('PDOStatement->execute(NULL)', $firstCodeframe->frame);
        self::assertCount(0, $firstCodeframe->code);
    }
}
