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

use BrBundle\Component\Document\Generator\Pdf\Invoice as InvoiceGenerator;
use BrBundle\Entity\Invoice;
use BrBundle\Entity\Invoice\History;
use BrBundle\Entity\Invoice\Manual as ManualInvoice;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File as FileUtil;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateTime;
use RuntimeException;
use Zend\Http\Headers;
use Zend\View\Model\ViewModel;

/**
 * InvoiceController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class InvoiceController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function viewAction()
    {
        $invoice = $this->getInvoiceEntity();
        if ($invoice === null) {
            return new ViewModel();
        }

        if (!$invoice->hasContract()) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'invoice' => $invoice,
            )
        );
    }

    public function manageAction()
    {
        $invoiceYear = $this->getParam('invoiceyear');

        if ($invoiceYear == null) {
            $invoiceYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.invoice_year_number');
        }

        $invoicePrefixes = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice')
            ->findAllInvoicePrefixes();

        $invoiceYears = array();
        foreach ($invoicePrefixes as $invoicePrefix) {
            $invoiceYears[] = substr($invoicePrefix['invoiceNumberPrefix'], 0, 4);
        }
        $invoiceYears = array_unique($invoiceYears);

        $invoices = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice')
            ->findAllUnPayedByInvoiceYearQuery($invoiceYear)
            ->getResult();

        $invoiceData = array();
        foreach ($invoices as $invoice) {
            $value = 0;
            if ($invoice->hasContract()) {
                $invoice->getOrder()->setEntityManager($this->getEntityManager());
                $value = $invoice->getOrder()->getTotalCostExclusive();
            } else {
                $value = $invoice->getPrice() / 100;
            }
            $invoiceData[] = array('invoice' => $invoice, 'value' => $value);
        }

        $paginator = $this->paginator()->createFromArray(
            $invoiceData,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'invoiceYears'      => $invoiceYears,
                'activeInvoiceYear' => $invoiceYear,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function csvAction()
    {
        $file = new CsvFile();
        $heading = array('Company', 'Author', 'Title', 'Date', 'Invoice Nb', 'Paid', 'Value');

        $invoices = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice')
            ->findAll();

        $results = array();

        foreach ($invoices as $invoice) {
            $value = 0;
            if ($invoice->hasContract()) {
                $invoice->getOrder()->setEntityManager($this->getEntityManager());
                $value = $invoice->getOrder()->getTotalCostExclusive();
            } else {
                $value = $invoice->getPrice() / 100;
            }

            $results[] = array($invoice->getCompany()->getName(),
                $invoice->getAuthor()->getPerson()->getFullName(),
                $invoice->getTitle(),
                $invoice->getCreationTime()->format('j/m/Y'),
                $invoice->getInvoiceNumber(),
                $invoice->isPayed(),
                $value,
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="contracts.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function payedListAction()
    {
        $invoices = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice')
            ->findAllPayedQuery()
            ->getResult();

        $invoiceData = array();
        foreach ($invoices as $invoice) {
            $value = 0;
            if ($invoice->hasContract()) {
                $invoice->getOrder()->setEntityManager($this->getEntityManager());
                $value = $invoice->getOrder()->getTotalCostExclusive();
            } else {
                $value = $invoice->getPrice();
            }
            $invoiceData[] = array('invoice' => $invoice, 'value' => $value);
        }

        $paginator = $this->paginator()->createFromArray(
            $invoiceData,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function historyAction()
    {
        $invoice = $this->getInvoiceEntity();
        if ($invoice == null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Invoice\History')
                ->findAllInvoiceVersions($invoice),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function manualAddAction()
    {
        $collaborator = $this->getCollaboratorEntity();

        if (!$collaborator) {
            return new ViewModel();
        }

        $form = $this->getForm('br_invoice_manual-add');

        if ($this->getRequest()->isPost()) {
            $formData = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($formData);

            if ($form->isValid()) {
                $invoice = $form->hydrateObject(
                    new ManualInvoice(
                        $this->getEntityManager(),
                        $collaborator
                    )
                );

                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.file_path')
                        . '/invoices/'
                        . $invoice->getInvoiceNumberPrefix();

                if (!file_exists($filePath)) {
                    if (!mkdir($filePath, 0770, true)) {
                        throw new RuntimeException('Failed to create the PDF directory');
                    }
                }

                do {
                    $fileName = '/' . $invoice->getInvoiceNumber() . '.pdf';
                } while (file_exists($filePath . $fileName));

                rename($formData['file']['tmp_name'], $filePath . $fileName);

                $this->getEntityManager()->persist($invoice);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The invoice was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_invoice',
                    array(
                        'action' => 'manage',
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

    public function editAction()
    {
        $invoice = $this->getInvoiceEntity(false);
        if ($invoice === null) {
            return new ViewModel();
        }

        if ($invoice->hasContract()) {
            $form = $this->getForm('br_invoice_contract-edit', array('invoice' => $invoice));
        } else {
            $form = $this->getForm('br_invoice_manual-edit', array('invoice' => $invoice));
        }

        if ($this->getRequest()->isPost()) {
            $formData = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($formData);

            if ($form->isValid()) {
                if (isset($formData['file']) && !($formData['file']['tmp_name'] == '')) {
                    $filePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('br.file_path') . '/invoices/'
                    . $invoice->getInvoiceNumberPrefix();

                    do {
                        $fileName = '/' . $invoice->getInvoiceNumber() . '.pdf';
                    } while (file_exists($filePath . $fileName));

                    rename($formData['file']['tmp_name'], $filePath . $fileName);
                }

                $history = new History($invoice);
                $this->getEntityManager()->persist($history);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The invoice was succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_invoice',
                    array(
                        'action' => 'manage',
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

    public function downloadAction()
    {
        $invoice = $this->getInvoiceEntity();
        if ($invoice === null) {
            return new ViewModel();
        }

        $language = $this->getParam('language');
        if ($invoice->hasContract()) {
            $generator = new InvoiceGenerator($this->getEntityManager(), $invoice, $language);
            $generator->generate();
        }

        $file = FileUtil::getRealFilename(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/invoices/'
                . $invoice->getInvoiceNumberPrefix() . '/'
                . $invoice->getInvoiceNumber() . '.pdf'
        );

        $fileHandler = fopen($file, 'r');
        $content = fread($fileHandler, filesize($file));

        $invoiceNb = $invoice->getInvoiceNumber();
        $companyName = $invoice->getCompany()->getName();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $invoiceNb . ' ' . $companyName . '.pdf"',
                'Content-Type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $content,
            )
        );
    }

    public function payAction()
    {
        $this->initAjax();

        $invoice = $this->getInvoiceEntity(false);
        if ($invoice === null) {
            return new ViewModel();
        }

        $date = DateTime::createFromFormat('d/m/Y', $this->getParam('date'));
        if ($date === null) {
            return new ViewModel();
        }

        $invoice->setPaidTime($date);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function payedAction()
    {
        $this->initAjax();

        $invoice = $this->getInvoiceEntity();
        if ($invoice === null) {
            return new ViewModel();
        }

        if ($this->getParam('payed') == 'true') {
            $invoice->setPayed();
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @param  boolean $allowPaid
     * @return \BrBundle\Entity\Invoice|null
     */
    private function getInvoiceEntity($allowPaid = true)
    {
        $invoice = $this->getEntityById('BrBundle\Entity\Invoice');

        if (!($invoice instanceof Invoice)) {
            $this->flashMessenger()->error(
                'Error',
                'No invoice was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if ($invoice->isPaid() && !$allowPaid) {
            $this->flashMessenger()->error(
                'Error',
                'The given invoice has been paid! Paid invoices cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $invoice;
    }

    /**
     * @return \BrBundle\Entity\Collaborator|null
     */
    private function getCollaboratorEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'You are not a collaborator, so you cannot add or edit invoices.'
            );

            $this->redirect()->toRoute(
                'br_admin_invoice',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $collaborator = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Collaborator')
            ->findCollaboratorByPersonId($this->getAuthentication()->getPersonObject()->getId());

        if ($collaborator === null) {
            $this->flashMessenger()->error(
                'Error',
                'You are not a collaborator, so you cannot add or edit invoices.'
            );

            $this->redirect()->toRoute(
                'br_admin_invoice',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $collaborator;
    }
}
