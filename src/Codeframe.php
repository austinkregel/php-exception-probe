<?php

namespace Kregel\ExceptionProbe;

/**
 * Class Codeframes
 * @package Kregel\ExceptionProbe
 */
class Codeframe
{
    /**
     * @var string
     */
    public $file;

    /**
     * @var int
     */
    public $line;

    /**
     * @var
     */
    public $frame;

    /**
     * Code lines
     */
    public $code;

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return Codeframe
     */
    public function setFile(string $file): Codeframe
    {
        $this->file = $file;
        return $this;
    }

    /**
     * StackAndCode constructor.
     * @param string $file
     * @param int $line
     * @param array $code
     * @param string $frame
     */
    public function __construct(string $file, int $line, array $code, string $frame)
    {
        $this->setFile($file);
        $this->line = $line;
        $this->code = $code;
        $this->frame = $frame;
    }
}
