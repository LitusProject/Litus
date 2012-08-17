<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile,
    CudiBundle\Component\Document\Generator\OrderPdf as OrderPdfGenerator,
    CudiBundle\Component\Document\Generator\OrderXml as OrderXmlGenerator,
    CudiBundle\Form\Admin\Stock\Orders\Add as AddForm,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * OrderController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OrderController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Supplier',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC'
            )
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers' => $suppliers,
            )
        );
    }

    public function supplierAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Orders\Order')
                ->findAllBySupplierAndPeriod($supplier, $period),
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'supplier' => $supplier,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'suppliers' => $suppliers,
            )
        );
    }

    public function editAction()
    {
        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'order' => $order,
                'supplier' => $order->getSupplier(),
                'suppliers' => $suppliers,
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        $academicYear = $this->getAcademicYear();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findOneById($formData['article_id']);

                $item = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Orders\Order')
                    ->addNumberByArticle($article, $formData['number'], $this->getAuthentication()->getPersonObject());

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The order item was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_stock_order',
                    array(
                        'action' => 'edit',
                        'id' => $item->getOrder()->getId(),
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'form' => $form,
                'suppliers' => $suppliers,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($item = $this->_getOrderItem()))
            return new ViewModel();

        $this->getEntityManager()->remove($item);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function placeAction()
    {
        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $order->order();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The order is successfully placed!'
            )
        );

        $this->redirect()->toRoute(
            'admin_stock_order',
            array(
                'action' => 'edit',
                'id' => $order->getId(),
            )
        );

        return new ViewModel();
    }

    public function pdfAction()
    {
        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $file = new TmpFile();
        $document = new OrderPdfGenerator($this->getEntityManager(), $order, $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="order.pdf"',
            'Content-type'        => 'application/pdf',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function exportAction()
    {
        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $document = new OrderXmlGenerator($this->getEntityManager(), $order);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="order.zip"',
            'Content-type'        => 'application/zip',
        ));
        $this->getResponse()->setHeaders($headers);

        $archive = new TmpFile();
        $document->generateArchive($archive);

        $handle = fopen($archive->getFileName(), 'r');
        $data = fread($handle, filesize($archive->getFileName()));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    public function cancelAction()
    {
        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $order->cancel();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The order was successfully canceled!'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    private function _getSupplier()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the supplier!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findOneById($this->getParam('id'));

        if (null === $supplier) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No supplier with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $supplier;
    }

    private function _getOrder()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the order!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $order = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Orders\Order')
            ->findOneById($this->getParam('id'));

        if (null === $order) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No order with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $order;
    }

    private function _getOrderItem()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the order item!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Orders\Item')
            ->findOneById($this->getParam('id'));

        if (null === $item) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No order item with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $item;
    }
}
