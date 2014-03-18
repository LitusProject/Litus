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

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Contract,
    BrBundle\Entity\Invoice,
    BrBundle\Entity\Invoice\InvoiceEntry,
    BrBundle\Form\Admin\Contract\Edit as EditForm,
    BrBundle\Component\Document\Generator\Pdf\Contract as ContractGenerator,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File as FileUtil,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * ContractController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ContractController extends \CommonBundle\Component\Controller\ActionController\AdminController
{

    public function manageAction()
    {
        if (null === $this->getParam('field')) {
            $paginator = $this->paginator()->createFromEntity(
                'BrBundle\Entity\Contract',
                $this->getParam('page')
            );
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }



    public function viewAction()
    {
        if (!($contract = $this->_getContract()))
            return new ViewModel();

        return new ViewModel(
            array(
                'contract' => $contract,
            )
        );
    }

    public function historyAction()
    {
        if (!($contract = $this->_getContract()))
            return new ViewModel();

        return new ViewModel(
            array(
                'contract' => $contract,
            )
        );
    }

    public function editAction()
    {
        if (!($contract = $this->_getContract(false)))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $contract);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $contract->setTitle($formData['title']);

                foreach ($contract->getEntries() as $entry)
                {
                    $entry->setContractText($formData['entry_' . $entry->getId()]);
                }

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
                        'action' => 'view',
                        'id' => $contract->getId(),
                    )
                );

                return new ViewModel();
            }
        }


        return new ViewModel(
            array(
                'contract' => $contract,
                'form' => $form,
            )
        );
    }

    public function signAction()
    {
        if (!($contract = $this->_getContract(false)))
            return new ViewModel();

        $invoice = new Invoice($contract->getOrder());

        foreach ($contract->getEntries() as $entry)
        {
            $invoiceEntry = new InvoiceEntry($invoice, $entry->getOrderEntry(), $entry->getPosition());
            $this->getEntityManager()->persist($invoiceEntry);
        }

        $this->getEntityManager()->persist($invoice);
        $this->getEntityManager()->flush();

        $contract->setSigned();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The contract was succesfully signed!'
            )
        );

        $this->redirect()->toRoute(
            'br_admin_contract',
            array(
                'action' => 'view',
                'id' => $contract->getId(),
            )
        );

        return new ViewModel();
    }

    public function downloadAction()
    {
        if (!($contract = $this->_getContract()))
            return new ViewModel();

        $generator = new ContractGenerator($this->getEntityManager(), $contract, $this->getTranslator());
        $generator->generate();

        $file = FileUtil::getRealFilename(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $this->getParam('id') . '/contract.pdf'
        );
        $fileHandler = fopen($file, 'r');
        $content = fread($fileHandler, filesize($file));

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="contract.pdf"',
            'Content-Type'        => 'application/pdf',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $content,
            )
        );
    }

    public function composeAction()
    {
        $this->initAjax();

        $postData = $this->getRequest()->getPost();
        parse_str($postData['sections'], $sections);

        $contract = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findOneById($postData['contractId']);

        // Don't allow reordering signed contract
        if ($contract->isSigned())
        {
            return new ViewModel();
        }

        $contractComposition = array();
        foreach ($sections['contractComposition'] as $position => $id) {
            $contractEntry = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract\ContractEntry')
                ->findOneById($id);

            $contractEntry->setPosition($position);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

   private function _getContract($allowSigned = true)
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
                'br_admin_order',
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
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        if ($contract->isSigned() && !$allowSigned) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given contract has been signed! Signed contracts cannot be modified.'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $contract;
    }
}
