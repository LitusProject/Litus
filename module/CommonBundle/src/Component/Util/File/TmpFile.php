<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Component\Util\File;

use CommonBundle\Component\Util\File as FileUtil;

/**
 * Utility class containing methods used for common actions on files
 *
 * @author Bram Gotink
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
    private $_fileHandler;
    
    /**
     * @param string $tmpDirectory The path to the directory that holds the temporary files
     * @throws \CommonBundle\Component\Util\File\Exception\FailedToOpenException Failed to open the temporary file
     */
    public function __construct($tmpDirectory = '/tmp')
    {
        $filename = '';
        do{
            $filename = '/.' . uniqid();
        } while (
            file_exists($tmpDirectory . $filename)
        );

        $this->_filename = FileUtil::getRealFilename($tmpDirectory . $filename);
        $this->_fileHandler = fopen($this->_filename, 'w+b');

        if(!$this->_fileHandler) {
            throw new Exception\FailedtoOpenException(
                'Failed to open file ' . $this->_filename
            );
        }
    }
    
    /**
     * Returns this file's content.
     *
     * @return string
     */
    public function getContent()
    {
        $this->_checkOpen();
        fseek($this->_fileHandler, 0);
        
        return fread(
            $this->_fileHandler, filesize($this->_filename)
        );
    }

    /**
     * Append content to the file.
     *    
     * @param string $content The content that should be appended
     * @return void
     */
    public function appendContent($content)
    {
        $this->_checkOpen();
        
        fwrite($this->_fileHandler, $content);
    }
    
    /**
     * Return the name of this file.
     *
     * @return string
     */
    public function getFilename()
    {
        $this->_checkOpen();
        
        return $this->_filename;
    }
    
    /**
     * Check whether or not this file is open.
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->_fileHandler !== null;
    }
    
    /**
     * Removes this file.
     *
     * @return void
     */
    public function destroy()
    {
        if ($this->isOpen()) {
            $fileHandler = $this->_fileHandler;
            $this->_fileHanlder = null;
            
            fclose($fileHandler);
            if (file_exists($this->_filename))
                unlink($this->_filename);
        }
    }
    
    /**
     * Checks whether or not this file is open, throwing an exception if it is not.
     *
     * @return void
     * @throws \CommonBundle\Component\Util\File\Exception\TmpFileClosedException If the file is not open
     */
    private function _checkOpen()
    {
        if (!$this->isOpen())
            throw new Exception\TmpFileClosedException($this);
    }
    
    /**
     * @return void
     */
    public function __destruct()
    {
        $this->destroy();
    }
}
