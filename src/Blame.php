<?php


namespace Kregel\ExceptionProbe;


class Blame
{
    public $hash;
    public $author;
    public $authorTime;
    public $authorEmail;
    public $authorTimezone;

    public $committer;
    public $committerTime;
    public $committerEmail;
    public $committerTimezone;

    public $summary;
    public $filename;
    public $contents;

    public $previous;

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = trim($authorEmail, '<>');
        return $this;
    }

    public function setAuthorTimezone($authorTimezone)
    {
        $this->authorTimezone = $authorTimezone;
        return $this;
    }

    public function setAuthorTime($authorTime)
    {
        $this->authorTime = $authorTime;
        return $this;
    }

    public function setCommitterTime($committerTime)
    {
        $this->committerTime = $committerTime;
        return $this;
    }

    public function setCommitter($committer)
    {
        $this->committer = $committer;
        return $this;
    }

    public function setCommitterEmail($committerEmail)
    {
        $this->committerEmail = trim($committerEmail, '<>');;
        return $this;
    }

    public function setCommitterTimezone($committerTimezone)
    {
        $this->committerTimezone = $committerTimezone;
        return $this;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }

    public function setPrevious($previous)
    {
        $this->previous = $previous;
        return $this;
    }

    public function toArray(): array
    {
        try {
            return [
                'date' => new \DateTime(date('Y-m-d H:i:s', $this->authorTime), new \DateTimeZone((int)trim($this->authorTimezone, '0'))),
                'author' => [
                    'name' => $this->author,
                    'email' => $this->authorEmail,
                ],
                'summary' => $this->summary,
                'contents' => $this->contents,
                'previous' => $this->previous,
            ];
        } catch (\Throwable $e) {
            return [
                'date' => new \DateTime(date('Y-m-d H:i:s', $this->authorTime)),
                'tz' => (int)trim($this->authorTimezone, '0'),
                'author' => [
                    'name' => $this->author,
                    'email' => $this->authorEmail,
                ],
                'summary' => $this->summary,
                'contents' => $this->contents,
                'previous' => $this->previous,
            ];
        }
    }
}
