<?php

namespace CommonBundle\Component\Util\File;

use CommonBundle\Component\Util\File as FileUtil;

/**
 * Utility class containing methods used for common actions on files
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class TmpFile
{
    /**
     * @var string The name of the file
     */
    private $filename;

    /**
     * @var resource The file handler
     */
    protected $fileHandle;

    /**
     * @param  string $tmpDirectory The path to the directory that holds the temporary files
     * @throws \CommonBundle\Component\Util\File\Exception\FailedToOpenException Failed to open the temporary file
     */
    public function __construct($tmpDirectory = '/tmp')
    {
        do {
            $filename = '/.' . uniqid();
        } while (file_exists($tmpDirectory . $filename));

        $this->filename = FileUtil::getRealFilename($tmpDirectory . $filename);
        $this->fileHandle = fopen($this->filename, 'wb');

        if ($this->fileHandle === false) {
            throw new Exception\FailedToOpenException(
                'Failed to open file "' . $this->filename . '"'
            );
        }
    }

    /**
     * Return the name of this file.
     *
     * @return string
     */
    public function getFilename()
    {
        $this->checkOpen();

        return $this->filename;
    }

    /**
     * Returns this file's content.
     *
     * @return string
     */
    public function getContent()
    {
        $this->checkOpen();

        $handle = fopen($this->filename, 'r');
        $data = fread($handle, filesize($this->filename));
        fclose($handle);

        return $data;
    }

    /**
     * Append content to the file.
     *
     * @param  string $content The content that should be appended
     * @return void
     */
    public function appendContent($content)
    {
        $this->checkOpen();
        fwrite($this->fileHandle, $content);
    }

    /**
     * Removes this file.
     *
     * @return void
     */
    public function destroy()
    {
        if ($this->isOpen()) {
            $fileHandle = $this->fileHandle;
            $this->fileHandle = null;

            fclose($fileHandle);
            if (file_exists($this->filename)) {
                unlink($this->filename);
            }
        }
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * Checks whether or not this file is open, throwing an exception if it is not.
     *
     * @return void
     * @throws \CommonBundle\Component\Util\File\Exception\TmpFileClosedException
     */
    protected function checkOpen()
    {
        if (!$this->isOpen()) {
            throw new Exception\TmpFileClosedException(
                'The file "' . $this->filename . '" has already been closed'
            );
        }
    }

    /**
     * Check whether or not this file is open.
     *
     * @return boolean
     */
    private function isOpen()
    {
        return $this->fileHandle !== null;
    }
}
