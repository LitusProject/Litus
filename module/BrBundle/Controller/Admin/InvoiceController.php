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

use BrBundle\Entity\Invoice,
    BrBundle\Entity\Invoice\InvoiceEntry,
    BrBundle\Component\Document\Generator\Pdf\Invoice as InvoiceGenerator,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File as FileUtil,
    DateTime,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * InvoiceController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class InvoiceController extends \CommonBundle\Component\Controller\ActionController\AdminController
{

    public function viewAction()
    {
        if (!($invoice = $this->_getInvoice()))
            return new ViewModel();

        return new ViewModel(
            array(
                'invoice' => $invoice,
            )
        );
    }

    public function downloadAction()
    {
        if (!($invoice = $this->_getInvoice()))
            return new ViewModel();

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

        if (!($invoice = $this->_getInvoice(false)))
            return new ViewModel();

        $invoice->setPaidTime(DateTime::createFromFormat('d/m/Y', $this->getParam('date')));
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

   private function _getInvoice($allowPaid = true)
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the invoice!'
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

        $invoice = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice')
            ->findOneById($this->getParam('id'));

        if (null === $invoice) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No invoice with the given ID was found!'
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

        if ($invoice->isPaid() && !$allowPaid) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given invoice has been paid! Paid invoices cannot be modified.'
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

        return $invoice;
    }
}
