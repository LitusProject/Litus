<?php

namespace Admin;

use \Admin\Form\Contract\Add as AddForm;
use \Admin\Form\Contract\Edit as EditForm;

use \Litus\Util\File as FileUtil;
use \Litus\Br\ContractGenerator;
use \Litus\Br\LetterGenerator;
use \Litus\Br\InvoiceGenerator;
use \Litus\Entity\Br\Contract;
use \Litus\Entity\Br\Contracts\Composition;

use \RuntimeException;
use \DirectoryIterator;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Paginator\Paginator;
use \Zend\Paginator\Adapter\ArrayAdapter;
use \Zend\Json\Json;
use \Zend\Registry;

class ContractController extends \Litus\Controller\Action
{
    private $_json = null;

    public function init()
    {
        parent::init();

        $contextSwitch = $this->broker('contextSwitch');
        $contextSwitch->setContext(
            'pdf',
            array(
                 'headers' => array(
                     'Content-Type' => 'application/pdf',
                     'Pragma' => 'public',
                     'Cache-Control' => 'private, max-age=0, must-revalidate'
                 )
            )
        );

        $contextSwitch->setActionContext('download', 'pdf')
            ->initContext();

        $this->broker('contextSwitch')
            ->addActionContext('compose', 'json')
            ->setAutoJsonSerialization(false)
            ->initContext();

        $this->_json = new Json();
    }

    private function _generateFiles($id, $invoiceOnly = false)
    {
        $contract = $this->getEntityManager()
            ->getRepository('Litus\Entity\Br\Contract')
            ->find($id);

        if (null === $contract)
            throw new \InvalidArgumentException('No contract found with the given ID');

        if ($contract->isDirty()) {
            if (!$invoiceOnly) {
                $generator = new ContractGenerator($contract);
                $generator->generate();

                $generator = new LetterGenerator($contract);
                $generator->generate();
            }

            if (-1 != $contract->getInvoiceNb()) {
                $generator = new InvoiceGenerator($contract);
                $generator->generate();
            }

            $contract->setDirty(false);
        }
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
            $form->setData($formData);

            if ($form->isValid()) {
                $company = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Users\People\Company')
                    ->findOneById($formData['company']);

                $newContract = new Contract(
                    $this->getAuthentication()->getPersonObject(),
                    $company,
                    $formData['discount'],
                    $formData['title']
                );

                $newContract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('Litus\Entity\Br\Contract')
                        ->findNextContractNb()
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

    public function manageAction()
    {
        $this->view->paginator = $this->_createPaginator('Litus\Entity\Br\Contract');
    }

    public function editAction()
    {
        $contractRepository = $this->getEntityManager()->getRepository('Litus\Entity\Br\Contract');
        $contract = $contractRepository->findOneById($this->getRequest()->getParam('id'));

        $form = new EditForm($contract);

        $this->view->form = $form;
        $this->view->contractEdited = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $company = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Users\People\Company')
                    ->findOneById($formData['company']);

                $contract->setCompany($company)
                    ->setDiscount($formData['discount'])
                    ->setTitle($formData['title'])
                    ->setContractNb($formData['contract_nb']);

                if($contract->isSigned())
                    $contract->setInvoiceNb($formData['invoice_nb']);

                $contractComposition = array();
                foreach ($formData['sections'] as $id) {
                    $section = $this->getEntityManager()
                        ->getRepository('Litus\Entity\Br\Contracts\Section')
                        ->findOneById($id);

                    $contractComposition[] = $section;
                }

                $contract->resetComposition()
                    ->setDirty();

                $this->_flush();

                $contract->addSections($contractComposition);

                $this->view->contractId = $contract->getId();
                $this->view->sections = $contractComposition;

                $this->view->contractUpdated = true;
            }
        }
    }

    public function deleteAction()
    {
        if (null !== $this->getRequest()->getParam('id')) {
            $contract = $this->getEntityManager()
                ->getRepository('Litus\Entity\Br\Contract')
                ->findOneById($this->getRequest()->getParam('id'));
        } else {
            $contract = null;
        }

        $this->view->contractDeleted = false;

        if (null === $this->getRequest()->getParam('confirm')) {
            $this->view->contract = $contract;
        } else {
            if (1 == $this->getRequest()->getParam('confirm')) {
                $this->getEntityManager()->remove($contract);
                $this->view->contractDeleted = true;
            } else {
                $this->_redirect('manage');
            }
        }
    }

    public function signAction()
    {
        if (0 == $this->getRequest()->getParam('id'))
            throw new \InvalidArgumentException('need a valid contract id');

        $contractRepository = $this->getEntityManager()
            ->getRepository('\Litus\Entity\Br\Contract');

        $contract = $contractRepository->find(
            $this->getRequest()->getParam('id')
        );

        if($contract->isSigned())
            throw new \InvalidArgumentException('Contract "' . $contract->getTitle() . '" has already been signed');

        $dirty = $contract->isDirty();

        $contract->setDirty()
            ->setInvoiceNb($contractRepository->findNextInvoiceNb());

        // Flush here, otherwise we might create two contracts with the same invoiceNb
        $this->_flush();

        $this->_forward('manage');
    }

    public function downloadAction()
    {
        if ('pdf' == $this->getRequest()->getParam('format')) {
            $this->broker('viewRenderer')->setNoRender();
            $this->_generateFiles(
                $this->getRequest()->getParam('id')
            );

            $file = FileUtil::getRealFilename(
                Registry::get('litus.resourceDirectory') . '/pdf/br/'
                    . $this->getRequest()->getParam('id') . '/'
                    . $this->getRequest()->getParam('type') .
                    '.pdf'
            );

            $this->getResponse()->setHeader(
                'Content-Disposition', 'inline; filename="' . $this->getRequest()->getParam('type') . '.pdf"'
            );
            $this->getResponse()->setHeader('Content-Length', filesize($file));

            readfile($file);
        } else {
            $this->view->paginator = $this->_createPaginator('Litus\Entity\Br\Contract');
        }
    }

    public function composeAction()
    {
        $this->_initAjax();

        $postData = $this->getRequest()->getPost();
        parse_str($postData['sections'], $sections);
        $updateCompositionResult = array(
            'result' => true
        );

        $contract = $this->getEntityManager()
            ->getRepository('Litus\Entity\Br\Contract')
            ->findOneById($postData['contractId']);

        $contractComposition = array();
        foreach ($sections['contractComposition'] as $position => $id) {
            $contractComposition[$position] = $this->getEntityManager()
                ->getRepository('Litus\Entity\Br\Contracts\Section')
                ->findOneById($id);
        }

        $contract->resetComposition()
            ->setDirty();

        // Avoiding duplicate key violations
        $this->getEntityManager()->flush();

        // Saving the new contract composition
        $contract->addSections($contractComposition);

        echo $this->_json->encode($updateCompositionResult);
    }
}
