<?php

namespace CommonBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;

class Csv
{
    /**
     * @var array The array containing the headers
     */
    private $headers;

    /**
     * @var array The array containing the results
     */
    private $results;

    /**
     * @param string[] $headers The array containing the headers
     * @param array    $results The array containing the form results
     */
    public function __construct(array $headers, array $results)
    {
        $this->headers = $headers;
        $this->results = $results;
    }

    /**
     * Generate a file to download.
     *
     * @param CsvFile $file The file to write to
     */
    public function generateDocument(CsvFile $file)
    {
        $file->appendContent($this->headers);

        foreach ($this->results as $result) {
            $file->appendContent($result);
        }
    }
}
