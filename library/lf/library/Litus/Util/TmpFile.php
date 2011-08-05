<?php

namespace Litus\Util;

use \Zend\Registry;

use \Litus\Util\File as FileUtil;
use \Litus\Util\TmpFileClosedException;
 
class TmpFile {

    const REGISTRY_KEY = 'litus.tmpDirectory';

    /**
     * @var string
     */
    private $_filename;

    /**
     * @var \resource
     */
    private $_file;

    public function __construct()
    {
        $file = '';
        do{
            $file = '/.' . randomString();
        } while (file_exists($this->_getTmpDir() . $file));

        $this->_filename = FileUtil::getRealFilename($this->_getTmpDir() . $file);
        $this->_file = fopen($this->_filename, 'bw+');
    }

    public function __destruct()
    {
        $this->destroy();
    }

    private static function _getTmpDir()
    {
        if(Registry::isRegistered(self::REGISTRY_KEY))
            return Registry::get(self::REGISTRY_KEY);
        return '/tmp';
    }

    public function getContent()
    {
        $this->_checkOpen();
        return fread($this->_file, filesize($this->_filename));
    }

    /**
     * @param string $content
     * @return void
     */
    public function appendContent($content)
    {
        $this->_checkOpen();
        fwrite($this->_file, $content);
    }

    public function getFilename()
    {
        $this->_checkOpen();
        return $this->_filename;
    }

    public function isOpen()
    {
        return $this->_file !== null;
    }

    private function _checkOpen()
    {
        if(!$this->isOpen())
            throw new TmpFileClosedException($this);
    }

    public function destroy()
    {
        if($this->isOpen()) {
            $file = $this->_file;
            $this->_file = null;
            fclose($file);
            unlink($this->_filename);
        }
    }
}
