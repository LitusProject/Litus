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

use BrBundle\Component\Document\Generator\Pdf\Invoice as InvoiceGenerator,
    BrBundle\Entity\Invoice,
    BrBundle\Entity\Invoice\ContractInvoice,
    BrBundle\Entity\Invoice\InvoiceHistory,
    BrBundle\Entity\Invoice\ManualInvoice,
    CommonBundle\Component\Util\File as FileUtil,
    DateTime,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

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
        if (!($invoice = $this->getInvoiceEntity()) && !$invoice->hasContract()) {
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
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Invoice')
                ->findAllUnPayedQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function payedListAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Invoice')
                ->findAllPayedQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function historyAction()
    {
        if (!($invoice = $this->getInvoiceEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Invoice\InvoiceHistory')
                ->findAllInvoiceVersions($invoice),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
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
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $invoice = $form->hydrateObject(
                    new ManualInvoice(
                        $this->getEntityManager(),
                        $collaborator
                    )
                );

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
        if (!($invoice = $this->getInvoiceEntity(false))) {
            return new ViewModel();
        }

        if ($invoice->hasContract()) {
            $form = $this->getForm('br_invoice_contract-edit', array('invoice' => $invoice));
        } else {
            $form = $this->getForm('br_invoice_manual-edit', array('invoice' => $invoice));
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $history = new InvoiceHistory($invoice);
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
        if (!($invoice = $this->getInvoiceEntity())) {
            return new ViewModel();
        }

        if ($invoice->hasContract()) {
            $generator = new InvoiceGenerator($this->getEntityManager(), $invoice);
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
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $invoiceNb . ' ' . $companyName . '.pdf"',
            'Content-Type'        => 'application/pdf',
        ));
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

        $date = DateTime::createFromFormat('d/m/Y', $this->getParam('date'));

        if (!($invoice = $this->getInvoiceEntity(false)) || !$date) {
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

        if (!($invoice = $this->getInvoiceEntity())) {
            return new ViewModel();
        }

        if ('true' == $this->getParam('payed')) {
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
     * @param  boolean      $allowPaid
     * @return Invoice|null
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
     * @return Collaborator|null
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

        if (null === $collaborator) {
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
