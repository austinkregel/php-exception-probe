<?php
declare(strict_types=1);

namespace Kregel\ExceptionProbe;

class GitBlameFinder
{    /**
     * @return array|Blame[]
     */
    public function blame(string $file)
    {
        if (function_exists('exec')) {
            exec(sprintf('git blame "%s" -e --line-porcelain', $file), $blame);
        } elseif (function_exists('passthru')) {
            passthru(sprintf('git blame "%s" -e --line-porcelain', $file), $blame);
        } else {
            throw new \RuntimeException("At the moment there is no way we can check the blame for this file.");
        }

        $count = count($blame);
        $items = [];
        $indexes = [];
        foreach ($blame as $index => $line) {
            // We're only going to do logic based off the location of the `author` header.
            if (stripos($line, 'author ') !== false) {
                $indexes[] = $index - 1;
            }
        }

        foreach ($indexes as $index => $startingCommitLine) {
            if(!isset($items[$index])) {
                $items[$index] = [];
            }

            $isFileContents = false;

            $blameObject = new Blame();
            for($i = $startingCommitLine; $i < ($indexes[$index + 1] ?? $count); $i ++) {
                if ($isFileContents) {
                    $blameObject->setContents($blame[$i]);
                    continue;
                }

                [$start, $end] = explode(' ', $blame[$i], 2);

                switch ($start) {
                    case 'author':
                        $blameObject->setAuthor($end);
                        break;
                    case 'author-mail':
                        $blameObject->setAuthorEmail($end);
                        break;
                    case 'author-time':
                        $blameObject->setAuthorTime($end);
                        break;
                    case 'author-tz':
                        $blameObject->setAuthorTimezone($end);
                        break;
                    case 'committer':
                        $blameObject->setCommitter($end);
                        break;
                    case 'committer-time':
                        $blameObject->setCommitterTime($end);
                        break;
                    case 'committer-tz':
                        $blameObject->setCommitterTimezone($end);
                        break;
                    case 'committer-mail':
                        $blameObject->setCommitterEmail($end);
                        break;
                    case 'summary':
                        $blameObject->setSummary($end);
                        break;
                    case 'filename':
                        $blameObject->setFilename($end);
                        break;
                    case 'previous':
                        $blameObject->setPrevious($end);
                        break;
                    default:
                        if (strlen($start) === 40) {
                            $blameObject->setHash($blame[$i]);
                        }
                }

                if ($start === 'filename') {
                    $isFileContents = true;
                }
            }
            $items[] = $blameObject;
        }

        return $items;
    }
}
