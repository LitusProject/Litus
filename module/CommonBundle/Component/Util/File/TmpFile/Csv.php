<?php

namespace CommonBundle\Component\Util\File\TmpFile;

/**
 * Extending the existing TmpFile class so that content is CSV formatted.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Csv extends \CommonBundle\Component\Util\File\TmpFile
{
    /**
     * Append a new array to the file.
     *
     * @param  array $content The content that should be appended
     * @return void
     */
    public function appendContent($content)
    {
        $this->checkOpen();
        fputcsv($this->fileHandle, $content);
    }
}
