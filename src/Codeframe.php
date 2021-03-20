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

    public function __construct(string $file, int $line, array $code, string $frame)
    {
        $this->setFile($file);
        $this->line = $line;
        $this->code = $code;
        $this->frame = $frame;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): Codeframe
    {
        $this->file = $file;
        return $this;
    }

    public function blame(): Blame
    {
        return (new GitBlameFinder)->blame($this->file)[$this->line];
    }
}
