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

use BrBundle\Component\Document\Generator\Pdf\Contract as ContractGenerator,
    BrBundle\Entity\Contract,
    BrBundle\Entity\Contract\ContractHistory,
    BrBundle\Entity\Invoice,
    BrBundle\Entity\Invoice\InvoiceEntry,
    CommonBundle\Component\Util\File as FileUtil,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * ContractController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class ContractController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'em' => $this->getEntityManager(),
            )
        );
    }

    public function viewAction()
    {
        if (!($contract = $this->getContractEntity())) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'contract' => $contract,
            )
        );
    }

    public function historyAction()
    {
        if (!($contract = $this->getContractEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract\ContractHistory')
                ->findAllContractVersions($contract),
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
        if (!($contract = $this->getContractEntity(false))) {
            return new ViewModel();
        }

        $form = $this->getForm('br_contract_edit', array('contract' => $contract));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $history = new ContractHistory($contract);
                $this->getEntityManager()->persist($history);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The contract was succesfully updated!'
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

    public function signedAction()
    {
        $this->initAjax();

        if (!($contract = $this->getContractEntity())) {
            return new ViewModel();
        }

        if ('true' == $this->getParam('signed')) {
            $invoice = new Invoice($contract->getOrder());

            foreach ($contract->getEntries() as $entry) {
                $invoiceEntry = new InvoiceEntry($invoice, $entry->getOrderEntry(), $entry->getPosition(),0);
                $this->getEntityManager()->persist($invoiceEntry);
            }

            $contract->setInvoiceNb(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->findNextInvoiceNb()
            );

            $this->getEntityManager()->persist($invoice);
        }

        $contract->setSigned('true' == $this->getParam('signed') ? true : false);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function signAction()
    {
        if (!($contract = $this->getContractEntity(false))) {
            return new ViewModel();
        }

        $form = $this->getForm('br_contract_sign-contract');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $invoice = $form->hydrateObject(
                    new Invoice($contract->getOrder())
                );

                foreach ($contract->getEntries() as $entry) {
                    $invoiceEntry = new InvoiceEntry($invoice, $entry->getOrderEntry(), $entry->getPosition(), 0);
                    $this->getEntityManager()->persist($invoiceEntry);
                }

                $this->getEntityManager()->persist($invoice);

                $contract->setSigned();

                $contract->setInvoiceNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextInvoiceNb()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The contract was succesfully signed!'
                );

                $this->redirect()->toRoute(
                    'br_admin_contract',
                    array(
                        'action' => 'view',
                        'id' => $contract->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function downloadAction()
    {
        if (!($contract = $this->getContractEntity())) {
            return new ViewModel();
        }

        $generator = new ContractGenerator($this->getEntityManager(), $contract, $this->getTranslator()->getTranslator());
        $generator->generate();

        $file = FileUtil::getRealFilename(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $this->getParam('id') . '/contract.pdf'
        );
        $fileHandler = fopen($file, 'r');
        $content = fread($fileHandler, filesize($file));

        $contractNb = $contract->getFullContractNumber($this->getEntityManager());
        $companyName = $contract->getCompany()->getName();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $contractNb . ' ' . $companyName . '.pdf"',
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

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            parse_str($postData['sections'], $sections);

            $contract = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findOneById($postData['contractId']);

            if ($contract->isSigned()) {
                return new ViewModel();
            }

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
        } else {
            return new ViewModel(
                array(
                    'result' => (object) array(
                        'status' => 'error',
                    ),
                )
            );
        }
    }

    /**
     * @param  boolean       $allowSigned
     * @return Contract|null
     */
    private function getContractEntity($allowSigned = true)
    {
        $contract = $this->getEntityById('BrBundle\Entity\Contract');

        if (!($contract instanceof Contract)) {
            $this->flashMessenger()->error(
                'Error',
                'No contract was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if ($contract->isSigned() && !$allowSigned) {
            $this->flashMessenger()->error(
                'Error',
                'The given contract has been signed! Signed contracts cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $contract;
    }
}
