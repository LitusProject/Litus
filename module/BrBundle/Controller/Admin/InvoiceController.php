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

use BrBundle\Component\Document\Generator\Pdf\Invoice as InvoiceGenerator,
    BrBundle\Entity\Invoice,
    BrBundle\Entity\Invoice\InvoiceHistory,
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
        if (!($invoice = $this->getInvoiceEntity())) {
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
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Invoice',
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'entityManager' => $this->getEntityManager(),
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

    public function editAction()
    {
        if (!($invoice = $this->getInvoiceEntity(false))) {
            return new ViewModel();
        }

        $form = $this->getForm('br_invoice_edit', array('invoice' => $invoice));

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

        $generator = new InvoiceGenerator($this->getEntityManager(), $invoice);
        $generator->generate();

        $file = FileUtil::getRealFilename(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $invoice->getOrder()->getContract()->getId() . '/invoice.pdf'
        );
        $fileHandler = fopen($file, 'r');
        $content = fread($fileHandler, filesize($file));

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="invoice.pdf"',
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
     * @param  boolean      $allowSigned
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
}
