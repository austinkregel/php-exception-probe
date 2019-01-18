<?php

namespace Kregel\ExceptionProbe;

class Stacktrace
{
    protected const REGEX_STACK_PART = '/#\d+ (.*)\((\d+)\): (.*)/';
    protected const REGEX_STACK_MESSAGE = '/(.*)in (\/.*:\d+$)/';
    protected const REGEX_STACK_LOOKBACK = '/(?<=(\.php))/';
    /**
     * @const int The number of lines of code we want to grab.
     */
    public const NUMBER_OF_LINES_TO_DISPLAY = 5;
    /**
     * @var array
     */
    public $message;
    /**
     * @var string
     */
    protected $stacktrace;
    /**
     * @var array
     */
    protected $brokenStackTrace;
    /**
     * @var array
     */
    protected $brokenMap;
    /**
     * @var Codeframe[]
     */
    protected $codeFrames;

    /**
     * Stacktrace constructor.
     * @param $stackTrace
     */
    public function __construct(?string $stackTrace = null)
    {
        $this->stacktrace = $stackTrace;
    }

    /**
     * @param null|string $stackTrace
     * @return Codeframe[]
     */
    public function parse(?string $stackTrace = null): array
    {
        if (empty($this->stacktrace) && !empty($stackTrace)) {
            $this->stacktrace = $stackTrace;
        }

        if (empty($this->stacktrace)) {
            throw new \InvalidArgumentException('You must pass a stacktrace otherwise we cannot do our job...');
        }

        return $this->breakUpTheStacks()->getFilesFromBrokenMap()->codeFrames;
    }

    /**
     * Convert the frame into a CodeFrame which includes relative code.
     * @return $this
     */
    protected function getFilesFromBrokenMap()
    {
        $mapParts = array_filter($this->brokenMap);

        $this->codeFrames = array_values(array_map(function ($frame) {
            if (count($frame) === 3) {
                [$file, $linesOfCode, $line] = $frame;
            }

            if (!$this->isValidFile($file)) {
                return new Codeframe($file, 0, [], trim($line));
            }

            $codes = $this->getTheCodeFromTheFile($file, (int) $linesOfCode);

            return new Codeframe($file, (int) $linesOfCode, $codes, trim($line));
        }, $mapParts));

        return $this;
    }

    /**
     * This searches the file we pass through for both the count of the lines in the file, and the lines of code surrounding that which broke.
     * @param $file
     * @param $lineNumber
     * @param int $currentLine
     * @return array
     */
    protected function getTheCodeFromTheFile(string $file, int $lineNumber, int $currentLine = 0): array
    {
        $handle = fopen($file, "r");

        $linesOfCode = [];

        while (!feof($handle)) {
            $currentLine++;

            $line = fgets($handle);

            if ($line === false) {
                break;
            }

            if (($currentLine - $lineNumber) > -static::NUMBER_OF_LINES_TO_DISPLAY && ($currentLine - $lineNumber) < static::NUMBER_OF_LINES_TO_DISPLAY) {
                $linesOfCode[$currentLine] = $line;
            }
        }

        fclose($handle);

        return $linesOfCode;
    }

    /**
     * We need to parse the lines that have a file in them, the lines that don't have a file in them, and a stack trace message.
     * @return $this
     */
    protected function breakUpTheStacks()
    {
        $this->brokenStackTrace = explode("\n", $this->stacktrace);

        $stack = array_values(array_filter($this->brokenStackTrace, function ($input) {
            return stripos($input, '#') !== false;
        }));

        $newMessage = array_values(array_filter(array_diff($this->brokenStackTrace, $stack), function ($input) {
            return stripos($input, 'stack trace') === false;
        }));

        if (empty($newMessage)) {
            $this->message = '';
        } else {
            $newMessage = $newMessage[0];
            preg_match_all(static::REGEX_STACK_MESSAGE, $newMessage, $match);

            $this->message = $match[1][0] ?? $newMessage;
        }

        $this->brokenMap = array_map(function ($frame) {
            preg_match_all(static::REGEX_STACK_PART, $frame, $matches);

            $parts = array_filter(array_map(function ($match) {
                return $match[0] ?? null;
            }, $matches));

            return array_splice($parts, 1, count($parts));
        }, $stack);

        return $this;
    }

    /**
     * See if the file is empty, if it exists, or if it's readable.
     * @param $file
     * @return bool
     */
    protected function isValidFile($file): bool
    {
        // If there's no file we can't do anything with it...
        if (empty($file)) {
            return false;
        }

        // If the file doesn't exist, no need to try and get it.
        if (!file_exists($file)) {
            // We should probably log here...
            return false;
        }

        // If we can't read the file... Why try...
        if (!is_readable($file)) {
            // We should probably log or something here...
            return false;
        }

        return true;
    }
}
