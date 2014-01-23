<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
    private $_filename;

    /**
     * @var resource The file handler
     */
    protected $fileHandle;

    /**
     * @param string $tmpDirectory The path to the directory that holds the temporary files
     * @throws \CommonBundle\Component\Util\File\Exception\FailedToOpenException Failed to open the temporary file
     */
    public function __construct($tmpDirectory = '/tmp')
    {
        $filename = '';
        do {
            $filename = '/.' . uniqid();
        } while (file_exists($tmpDirectory . $filename));

        $this->_filename = FileUtil::getRealFilename($tmpDirectory . $filename);
        $this->fileHandle = fopen($this->_filename, 'wb');

        if(false === $this->fileHandle) {
            throw new Exception\FailedToOpenException(
                'Failed to open file "' . $this->_filename . '"'
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
        return $this->_filename;
    }

    /**
     * Returns this file's content.
     *
     * @return string
     */
    public function getContent()
    {
        $this->checkOpen();

        $handle = fopen($this->_filename, 'r');
        $data = fread($handle, filesize($this->_filename));
        fclose($handle);

        return $data;
    }

    /**
     * Append content to the file.
     *
     * @param string $content The content that should be appended
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
        if ($this->_isOpen()) {
            $fileHandle = $this->fileHandle;
            $this->_fileHandle = null;

            fclose($fileHandle);
            if (file_exists($this->_filename))
                unlink($this->_filename);
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
        if (!$this->_isOpen()) {
            throw new Exception\TmpFileClosedException(
                'The file "' . $this->_filename . '" has already been closed'
            );
        }
    }

    /**
     * Check whether or not this file is open.
     *
     * @return bool
     */
    private function _isOpen()
    {
        return null !== $this->fileHandle;
    }
}
