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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin;

use BrBundle\Component\Document\Generator\Pdf\Contract as ContractGenerator;
use BrBundle\Entity\Contract;
use BrBundle\Entity\Contract\ContractHistory;
use BrBundle\Entity\Invoice\ContractInvoice;
use BrBundle\Entity\Invoice\InvoiceEntry;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File as FileUtil;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Zend\Http\Headers;
use Zend\View\Model\ViewModel;

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
        $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewUnsignedQuery()
                ->getResult();

        $contractData = array();

        foreach ($contracts as $contract) {
            $contract->getOrder()->setEntityManager($this->getEntityManager());
            $value = $contract->getOrder()->getTotalCostExclusive();
            $contractData[] = array('contract' => $contract, 'value' => $value);
        }

        $paginator = $this->paginator()->createFromArray(
            $contractData,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'em'                => $this->getEntityManager(),
            )
        );
    }

    public function signedListAction()
    {
        $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllSignedQuery()
                ->getResult();

        $contractData = array();

        foreach ($contracts as $contract) {
            $contract->getOrder()->setEntityManager($this->getEntityManager());
            $value = $contract->getOrder()->getTotalCostExclusive();
            $contractData[] = array('contract' => $contract, 'value' => $value);
        }

        $paginator = $this->paginator()->createFromArray(
            $contractData,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'em'                => $this->getEntityManager(),
            )
        );
    }

    public function viewAction()
    {
        $contract = $this->getContractEntity();
        if ($contract === null) {
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
        $contract = $this->getContractEntity();
        if ($contract === null) {
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
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function csvAction()
    {
        $file = new CsvFile();
        $heading = array('Company', 'Author', 'Title', 'Date', 'Contract Nb', 'Signed', 'Value');

        $contracts = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findAll();

        $results = array();

        foreach ($contracts as $contract) {
            $contract->getOrder()->setEntityManager($this->getEntityManager());
            $value = $contract->getOrder()->getTotalCostExclusive();

            $results[] = array($contract->getCompany()->getName(),
                $contract->getAuthor()->getPerson()->getFullName(),
                $contract->getTitle(),
                $contract->getDate()->format('j/m/Y'),
                $contract->getFullContractNumber($this->getEntityManager()),
                $contract->isSigned(),
                $value,
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="contracts.csv"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function editAction()
    {
        $contract = $this->getContractEntity(false);
        if ($contract === null) {
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
                        'id'     => $contract->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'contract' => $contract,
                'form'     => $form,
            )
        );
    }

    public function signedAction()
    {
        $this->initAjax();

        $contract = $this->getContractEntity();
        if ($contract === null) {
            return new ViewModel();
        }

        if ($this->getParam('signed') == 'true') {
            $invoice = new ContractInvoice($contract->getOrder(), $this->getEntityManager());

            foreach ($contract->getEntries() as $entry) {
                $invoiceEntry = new InvoiceEntry($invoice, $entry->getOrderEntry(), $entry->getPosition(), 0);
                $this->getEntityManager()->persist($invoiceEntry);
            }

            $bookNumber = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.invoice_book_number');

            $yearNumber = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.invoice_year_number');

            $prefix = $yearNumber . $bookNumber;

            $contract->setInvoiceNb(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->findNextInvoiceNb($prefix)
            );

            $this->getEntityManager()->persist($invoice);
        }

        $contract->setSigned($this->getParam('signed') == 'true' ? true : false);

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
        $contract = $this->getContractEntity(false);
        if ($contract === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_contract_sign-contract');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $invoice = $form->hydrateObject(
                    new ContractInvoice($contract->getOrder(), $this->getEntityManager())
                );

                foreach ($contract->getEntries() as $entry) {
                    $invoiceEntry = new InvoiceEntry($invoice, $entry->getOrderEntry(), $entry->getPosition(), 0);
                    $this->getEntityManager()->persist($invoiceEntry);
                }

                $this->getEntityManager()->persist($invoice);

                $contract->setSigned();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The contract was succesfully signed!'
                );

                $this->redirect()->toRoute(
                    'br_admin_invoice',
                    array(
                        'action' => 'edit',
                        'id'     => $invoice->getId(),
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
        $contract = $this->getContractEntity();
        if ($contract === null) {
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
     * @param  boolean $allowSigned
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
