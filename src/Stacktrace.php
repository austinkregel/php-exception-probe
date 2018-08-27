<?php

namespace Kregel\ExceptionProbe;

class Stacktrace
{
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
     * @var array
     */
    protected $message;

    /**
     * @var Codeframe[]
     */
    protected $codeFrames;

    /**
     * @const int The number of lines of code we want to grab.
     */
    public const NUMBER_OF_LINES_TO_DISPLAY = 5;

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
     * We need to parse the lines that have a file in them, the lines that don't have a file in them, and a stack trace message.
     * @return $this
     */
    protected function breakUpTheStacks()
    {
        $this->brokenStackTrace = explode("\n", $this->stacktrace);

        $stack = array_values(array_filter($this->brokenStackTrace, function ($input) {
            return stripos($input, '#') !== false;
        }));

        $this->message = array_values(array_filter(array_diff($this->brokenStackTrace, $stack), function ($input) {
            return stripos($input, 'stack trace') === false;
        }));

        $this->brokenMap = array_map(function ($frame) {
            return preg_split('/(?<=[\):?])/', $frame, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        }, $stack);

        return $this;
    }

    /**
     * Convert the frame into a CodeFrame which includes relative code.
     * @return $this
     */
    protected function getFilesFromBrokenMap()
    {
        $this->codeFrames = array_values(array_filter(array_map(function ($line) {
            if (count($line) == 1) {
                return;
            }

            [$mainFrame] = $line;
            // We only want to parse the files that are PHP files for now... We don't care about other files...
            if (stripos($mainFrame, '.php') !== false) {
                [$frame, $line, $file] = $this->parseFrame($mainFrame);

                if (!$this->validateTheFile($file)) {
                    return;
                }

                $linesOfCode = $this->getTheCodeFromTheFile($file, $line);

                return new Codeframe($file, $line, $linesOfCode, $frame);
            }
        }, $this->brokenMap)));

        return $this;
    }

    /**
     * Break the frame of a stack trace into three pars, the frame, the line number, and the file itself.
     * @param $mainFrame
     * @return array
     */
    protected function parseFrame($mainFrame): array
    {
        [$frame, $line] = preg_split('/(?<=(\.php))/', $mainFrame, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        [$file] = array_values(array_filter(preg_split('/#\d+\s/', $frame, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)));
        $lineNumber = (int) str_replace(['(', ')'], '', $line);

        return [$frame, $lineNumber, trim($file)];
    }

    /**
     * This searches the file we pass through for both the count of the lines in the file, and the lines of code surrounding that which broke.
     * @param $file
     * @param $lineNumber
     * @param int $currentLine
     * @return array
     */
    protected function getTheCodeFromTheFile($file, $lineNumber, $currentLine = 0): array
    {
        $handle = fopen($file, "r");

        $linesOfCode = [];

        while (!feof($handle)) {
            $currentLine++;

            $line = fgets($handle);

            if (($currentLine - $lineNumber) > -static::NUMBER_OF_LINES_TO_DISPLAY && ($currentLine - $lineNumber) < static::NUMBER_OF_LINES_TO_DISPLAY) {
                $linesOfCode[$currentLine] = $line;
            }
        }

        fclose($handle);

        return $linesOfCode;
    }

    /**
     * See if the file is empty, if it exists, or if it's readable.
     * @param $file
     * @return bool
     */
    protected function validateTheFile($file): bool
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
