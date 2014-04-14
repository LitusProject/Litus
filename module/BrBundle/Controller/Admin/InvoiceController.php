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

use BrBundle\Entity\Invoice,
    BrBundle\Entity\Invoice\InvoiceEntry,
    BrBundle\Form\Admin\Invoice\Edit as EditForm,
    BrBundle\Component\Document\Generator\Pdf\Invoice as InvoiceGenerator,
    CommonBundle\Component\FlashMessenger\FlashMessage,
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
        if (!($invoice = $this->_getInvoice()))
            return new ViewModel();

        return new ViewModel(
            array(
                'invoice' => $invoice,
            )
        );
    }

    public function manageAction()
    {
        if (null === $this->getParam('field')) {
            $paginator = $this->paginator()->createFromEntity(
                'BrBundle\Entity\Invoice',
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

    public function editAction()
    {
        if (!($invoice = $this->_getInvoice(false)))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $invoice);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $invoiceVersion = $invoice->getVersion();

                $newVersionNb = 0;

                foreach ($invoice->getEntries() as $entry)
                {
                    if($entry->getVersion() == $invoiceVersion){
                        $newVersionNb = $entry->getVersion() + 1;
                        $newInvoiceEntry = new InvoiceEntry($invoice,$entry->getOrderEntry(),$entry->getPosition(),$newVersionNb);

                        $this->getEntityManager()->persist($newInvoiceEntry);

                        $newInvoiceEntry->setInvoiceText($formData['entry_' . $entry->getId()]);
                    }
                }

                $invoice->setVersion($newVersionNb);

                $this->getEntityManager()->flush();


                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The invoice was succesfully updated!'
                    )
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

    public function payedAction()
    {
        $this->initAjax();

        $invoice = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice')
            ->findOneById($this->getParam('id'));

        if ('true' == $this->getParam('payed')) {
            $invoice->setPayed();
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
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
