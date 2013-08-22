<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin;

// use \Admin\Form\Contract\Add as AddForm;
// use \Admin\Form\Contract\Edit as EditForm;

// use \Litus\Util\File as FileUtil;
// use \Litus\Br\ContractGenerator;
// use \Litus\Br\LetterGenerator;
// use \Litus\Br\InvoiceGenerator;
// use \Litus\Entity\Br\Contract;
// use \Litus\Entity\Br\Contracts\Composition;

// use \RuntimeException;
// use \DirectoryIterator;

// use \Litus\Application\Resource\Doctrine as DoctrineResource;

// use \Zend\Paginator\Paginator;
// use \Zend\Paginator\Adapter\ArrayAdapter;
// use \Zend\Json\Json;
// use \Zend\Registry;

use BrBundle\Entity\Contract,
    BrBundle\Form\Admin\Contract\Add as AddForm,
    BrBundle\Form\Admin\Contract\Edit as EditForm,
    BrBundle\Component\Document\Generator\Pdf\Contract as ContractGenerator,
    BrBundle\Component\Document\Generator\Pdf\Invoice as InvoiceGenerator,
    BrBundle\Component\Document\Generator\Pdf\Letter as LetterGenerator,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * ContractController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ContractController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    // private $_json = null;

    // public function init()
    // {
    //     parent::init();

    //     $contextSwitch = $this->broker('contextSwitch');
    //     $contextSwitch->setContext(
    //         'pdf',
    //         array(
    //              'headers' => array(
    //                  'Content-Type' => 'application/pdf',
    //                  'Pragma' => 'public',
    //                  'Cache-Control' => 'private, max-age=0, must-revalidate'
    //              )
    //         )
    //     );

    //     $contextSwitch->setActionContext('download', 'pdf')
    //         ->initContext();

    //     $this->broker('contextSwitch')
    //         ->addActionContext('compose', 'json')
    //         ->setAutoJsonSerialization(false)
    //         ->initContext();

    //     $this->_json = new Json();
    // }

    private function _generateFiles($id, $invoiceOnly = false)
    {
        if (!($contract = $this->_getContract()))
            return new ViewModel();

        if ($contract->isDirty()) {
            if (!$invoiceOnly) {
                $generator = new ContractGenerator($this->getEntityManager(), $contract);
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

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);

                $newContract = new Contract(
                    $this->getAuthentication()->getPersonObject(),
                    $company,
                    $formData['discount'],
                    $formData['title']
                );

                $newContract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNb()
                );

                $contractComposition = array();
                foreach ($formData['sections'] as $id) {
                    $section = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract\Section')
                        ->findOneById($id);

                    $contractComposition[] = $section;
                }
                $newContract->addSections($contractComposition);

                $this->getEntityManager()->persist($newContract);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The contract was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_contract',
                    array(
                        'action' => 'sort',
                        'id'     => $newContract->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function sortAction()
    {
        if (!($contract = $this->_getContract()))
            return new ViewModel();

        if ($this->getRequest()->isPost()) {

            $this->redirect()->toRoute(
                'br_admin_contract',
                array(
                    'action' => 'manage',
                )
            );

        }

        return new ViewModel(
            array(
                'contract' => $contract,
            )
        );
    }

    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function editAction()
    {
        $contractRepository = $this->getEntityManager()->getRepository('BrBundle\Entity\Contract');
        if (!($contract = $this->_getContract()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $contract);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
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
                        ->getRepository('BrBundle\Entity\Contract\Section')
                        ->findOneById($id);

                    $contractComposition[] = $section;
                }

                $contract->resetComposition()
                    ->setDirty();

                $this->getEntityManager()->flush();

                $contract->addSections($contractComposition);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The contract was succesfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_contract',
                    array(
                        'action' => 'sort',
                        'id'     => $contract->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($contract = $this->_getContract()))
            return new ViewModel();

        $this->getEntityManager()->remove($contract);
        $this->getEntityManager()->flush();


        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    // public function signAction()
    // {
    //     if (0 == $this->getRequest()->getParam('id'))
    //         throw new \InvalidArgumentException('need a valid contract id');

    //     $contractRepository = $this->getEntityManager()
    //         ->getRepository('\Litus\Entity\Br\Contract');

    //     $contract = $contractRepository->find(
    //         $this->getRequest()->getParam('id')
    //     );

    //     if($contract->isSigned())
    //         throw new \InvalidArgumentException('Contract "' . $contract->getTitle() . '" has already been signed');

    //     $dirty = $contract->isDirty();

    //     $contract->setDirty()
    //         ->setInvoiceNb($contractRepository->findNextInvoiceNb());

    //     // Flush here, otherwise we might create two contracts with the same invoiceNb
    //     $this->_flush();

    //     $this->_forward('manage');
    // }

    public function downloadAction()
    {
        $this->_generateFiles(
            $this->getParam('id')
        );

        // TODO: ability to download letter, contract and invoice
        $file = FileUtil::getRealFilename(
            $this->getEntityManager()
                ->getRepository('CommonBUndle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $this->getRequest()->getParam('id') . '/contract.pdf'
        );

        $this->getResponse()->setHeader(
            'Content-Disposition', 'inline; filename="' . $this->getRequest()->getParam('type') . '.pdf"'
        );
        $this->getResponse()->setHeader('Content-Length', filesize($file));

        readfile($file);
    }

    public function composeAction()
    {
        $this->initAjax();

        $postData = $this->getRequest()->getPost();
        parse_str($postData['sections'], $sections);

        $contract = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findOneById($postData['contractId']);

        $contractComposition = array();
        foreach ($sections['contractComposition'] as $position => $id) {
            $contractComposition[$position] = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract\Section')
                ->findOneById($id);
        }

        $contract->resetComposition()
            ->setDirty();

        // Avoiding duplicate key violations
        $this->getEntityManager()->flush();

        // Saving the new contract composition
        $contract->addSections($contractComposition);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

   private function _getContract()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the contract!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_contract',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $contract = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findOneById($this->getParam('id'));

        if (null === $contract) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No contract with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_contract',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $contract;
    }
}
