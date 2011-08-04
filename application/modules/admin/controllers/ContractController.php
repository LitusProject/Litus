<?php

namespace Admin;

use \Admin\Form\Contract\Index as IndexForm;
use \Admin\Form\Contract\ListForm;

use \RuntimeException;
use \DirectoryIterator;

use \Zend\Registry;

class ContractController extends \Litus\Controller\Action
{
    private $_id;

    private $_file;


    public function init()
    {
        parent::init();

        $this->_id = '0';
        $this->_file = '';
        if($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if(isset($postData['id']))
                $this->_id = $postData['id'];

            if(isset($postData['type']))
                $this->_file = $postData['type'];
        } else {
            $this->_id = $this->getRequest()->getParam('id','0');
            $this->_file = $this->getRequest()->getParam('type','contract');
        }
        $this->view->pdfId = $this->_id;

        /** @var $contextSwitch \Zend\Controller\Action\Helper\ContextSwitch */
        $contextSwitch = $this->broker('contextSwitch');
        $contextSwitch ->setContext('pdf', array(
                                      'headers' => array(
                                          'Content-type' => 'application/pdf',
                                          'Pragma' => 'public',
                                          'Cache-Control' => 'private, max-age=0, must-revalidate',
                                          'Content-Disposition' => 'inline; filename="' . $this->_file . '"',
                                      ),
                                    ))
                ->setActionContext('download', 'pdf')
                ->setAutoDisableLayout('true')
                ->initContext('pdf');
    }

    private function _filterArray(DirectoryIterator $input, $fileType)
    {
        $result = array();
        for (;$input->valid();$input->next()) {
            if(!$input->isDot()) {
                if ((($fileType == 'file') && $input->isFile())
                        || (($fileType == 'dir') && $input->isDir()))
                    $result[] = $input->getFilename();
            }
        }
        return $result;
    }

    private function _getDirectoryIterator($location)
    {
        if(!is_readable($location))
            throw new RuntimeException($location . ' is not readable by the server.');
        if(!is_dir($location))
            throw new RuntimeException('Permanent error: ' . $location . ' is not a directory.');

        return new DirectoryIterator($location);
    }

    private function _getRootDirectory()
    {
        return Registry::get('litus.resourcesDirectory') . '/pdf/br';
    }

    public function indexAction()
    {
        $ids = $this->_getDirectoryIterator($this->_getRootDirectory());

        $this->view->form = new IndexForm($this->_filterArray($ids, 'dir'));
    }

    public function listAction()
    {
        if($this->_id == '0')
            throw new \InvalidArgumentException('need a valid contract id');

        $types = $this->_getDirectoryIterator($this->_getRootDirectory() . '/' . $this->_id);

        $this->view->form = new ListForm($this->_id, $this->_filterArray($types, 'file'));
    }

    public function downloadAction()
    {
        $this->broker('viewRenderer')->setNoRender();

        if($this->_id == '0')
            throw new \InvalidArgumentException('need a valid contract id');

        $file = $this->_getRootDirectory() . '/' . $this->_id . '/' . $this->_file;

        $this->getResponse()->setHeader('Content-Length', filesize($file));

        readfile($file);
    }
}
