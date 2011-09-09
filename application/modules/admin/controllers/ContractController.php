<?php

namespace Admin;

use \Admin\Form\Contract\Add as AddForm;
use \Admin\Form\Contract\Index as IndexForm;
use \Admin\Form\Contract\ListForm;

use \Litus\Util\File as FileUtil;
use \Litus\Br\ContractGenerator;
use \Litus\Br\LetterGenerator;
use \Litus\Br\InvoiceGenerator;
use \Litus\Entity\Br\Contracts\Contract;
use \Litus\Entity\Br\Contracts\ContractComposition;

use \RuntimeException;
use \DirectoryIterator;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Json\Json;
use \Zend\Registry;

class ContractController extends \Litus\Controller\Action
{
    private $_json = null;

    private $_id = '';
    private $_file = '';

    public function init()
    {
        parent::init();

        $this->_id = '0';
        $this->_file = null;
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['id']))
                $this->_id = $postData['id'];

            if (isset($postData['type']))
                $this->_file = $postData['type'];
        } else {
            $this->_id = $this->getRequest()->getParam('id', '0');
            $this->_file = $this->getRequest()->getParam('type');
        }

        /** @var $contextSwitch \Zend\Controller\Action\Helper\ContextSwitch */
        $contextSwitch = $this->broker('contextSwitch');
        $contextSwitch->setContext(
            'pdf',
            array(
                 'headers' => array(
                     'Content-type' => 'application/pdf',
                     'Pragma' => 'public',
                     'Cache-Control' => 'private, max-age=0, must-revalidate',
                     'Content-Disposition' => 'inline; filename="' . $this->_file . '"'
                 )
            )
        );
        $contextSwitch->setActionContext('download', 'pdf')
            ->setAutoDisableLayout('true')
            ->initContext('pdf');


        $this->broker('contextSwitch')
            ->addActionContext('updatecomposition', 'json')
            ->setAutoJsonSerialization(false)
            ->initContext();

        $this->_json = new Json();
    }

    private function _filterArray(DirectoryIterator $input, $fileType)
    {
        $result = array();
        for (; $input->valid(); $input->next()) {
            if (!$input->isDot()) {
                if ((($fileType == 'file') && $input->isFile())
                    || (($fileType == 'dir') && $input->isDir())
                )
                    $result[] = $input->getFilename();
            }
        }
        return $result;
    }

    private function _getDirectoryIterator($location)
    {
        $location = FileUtil::getRealFilename($location);

        if (!is_readable($location))
            throw new RuntimeException($location . ' is not readable by the server.');
        if (!is_dir($location))
            throw new RuntimeException('Permanent error: ' . $location . ' is not a directory.');

        return new DirectoryIterator($location);
    }

    private function _getRootDirectory()
    {
        return Registry::get('litus.resourceDirectory') . '/pdf/br';
    }

    public function indexAction()
    {
        $this->_forward('add');
    }

    public function addAction()
    {
        $form = new AddForm();

        $this->view->form = $form;
        $this->view->contractCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $company = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Users\People\Company')
                    ->findOneById($formData['company']);

                $newContract = new Contract(
                    $this->getAuthentication()->getPersonObject(),
                    $company,
                    $formData['discount'],
                    $formData['title']
                );
                
                $contractComposition = array();
                foreach ($formData['sections'] as $id) {
                    $section = $this->getEntityManager()
                        ->getRepository('Litus\Entity\Br\Contracts\Section')
                        ->findOneById($id);

                    $contractComposition[] = $section;
                }
                $newContract->addSections($contractComposition);

                $this->getEntityManager()->persist($newContract);

                $this->view->contractId = $newContract->getId();
                $this->view->sections = $contractComposition;
                $this->view->contractCreated = true;
            }
        }
    }

    public function updatecompositionAction()
    {
        $this->_initAjax();

        $postData = $this->getRequest()->getPost();
        parse_str($postData['sections'], $sections);
        $updateCompositionResult = array(
            'result' => true
        );

        $contract = $this->getEntityManager()
            ->getRepository('Litus\Entity\Br\Contracts\Contract')
            ->findOneById($postData['contractId']);

        $contractComposition = array();
        foreach ($sections['contractComposition'] as $position => $id) {
            $contractComposition[$position] = $this->getEntityManager()
                ->getRepository('Litus\Entity\Br\Contracts\Section')
                ->findOneById($id);
        }

        // Avoiding duplicate key violations
        $this->getEntityManager()->persist(
            $contract->resetComposition()
        );
        $this->getEntityManager()->flush();

        // Saving the new contract composition
        $this->getEntityManager()->persist(
            $contract->addSections($contractComposition)
        );
        
        echo $this->_json->encode($updateCompositionResult);
    }

    public function listAction()
    {
        if ($this->_id == '0') {
            $this->view->form = new IndexForm($this->getEntityManager()
                    ->getRepository('Litus\Entity\Br\Contracts\Contract')
                    ->getAllContractIds());
        } else {
            $directory = $this->_getRootDirectory() . '/' . $this->_id;

            if (file_exists($directory)) {
                $types = $this->_getDirectoryIterator($directory);
                $this->view->form = new ListForm($this->_id, $this->_filterArray($types, 'file'));
            } else {
                $this->_forward('generate', null, null, array('id' => $this->_id));
            }
        }
    }

    public function downloadAction()
    {
        if ($this->_id == '0')
            $this->_redirect('/admin/contract/list');
        elseif ($this->_file === null)
            $this->_redirect('/admin/contract/list/id/' . $this->_id);
        else {
            $this->broker('viewRenderer')->setNoRender();

            $file = $this->_getRootDirectory() . '/' . $this->_id . '/' . $this->_file;
            $file = FileUtil::getRealFilename($file);

            $this->getResponse()->setHeader('Content-Length', filesize($file));

            readfile($file);
        }
    }

    public function generateAction()
    {
        if ($this->_id == '0')
            throw new \InvalidArgumentException('need a valid contract id');

        /** @var $contractRepository \Litus\Repository\Br\Contracts\Contract */
        $contractRepository = $this->getEntityManager()->getRepository('\Litus\Entity\Br\Contracts\Contract');
        /** @var $contract \Litus\Entity\Br\Contracts\Contract */
        $contract = $contractRepository->find($this->_id);
        if ($contract === null)
            throw new \InvalidArgumentException('No contract found with id ' . $this->_id . '.');

        /** @var $generator \Litus\Br\DocumentGenerator */
        $generator = new ContractGenerator($contract);
        $generator->generate();

        $generator = new LetterGenerator($contract);
        $generator->generate();

        $generator = new InvoiceGenerator($contract);
        $generator->generate();

        $this->_redirect('/admin/contract/list/id/' . $this->_id);
    }
}
