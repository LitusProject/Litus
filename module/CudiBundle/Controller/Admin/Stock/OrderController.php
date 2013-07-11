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

namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile,
    CudiBundle\Component\Document\Generator\Order\Pdf as OrderPdfGenerator,
    CudiBundle\Component\Document\Generator\Order\Xml as OrderXmlGenerator,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Form\Admin\Stock\Order\Add as AddForm,
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

    public function overviewAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (null !== $this->getParam('field'))
            $orders = $this->_search($period);

        if (!isset($orders)) {
            $orders = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByPeriod($period);
        }

        $paginator = $this->paginator()->createFromArray(
            $orders,
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers' => $suppliers,
                'period' => $period,
            )
        );
    }

    public function searchAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $orders = $this->_search($period);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($orders, $numResults);

        $result = array();
        foreach($orders as $order) {
            $item = (object) array();
            $item->id = $order->getId();
            $item->articleId = $order->getArticle()->getId();
            $item->title = $order->getArticle()->getMainArticle()->getTitle();
            $item->dateOrdered = $order->getOrder()->getDateOrdered() ? $order->getOrder()->getDateOrdered()->format('d/m/Y H:i') : '';
            $item->supplier = $order->getArticle()->getSupplier()->getName();
            $item->nbAssigned = $period->getNbAssigned($order->getArticle());
            $item->nbNotAssigned = $period->getNbBooked($order->getArticle());
            $item->nbInStock = $order->getArticle()->getStockValue();
            $item->nbNotDelivered = $period->getNbOrdered($order->getArticle()) - $period->getNbDelivered($order->getArticle());
            $item->nbNotDelivered = $item->nbNotDelivered < 0 ? 0 : $item->nbNotDelivered;
            $item->nbReserved = $period->getNbBooked($order->getArticle()) + $period->getNbAssigned($order->getArticle());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
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
                ->getRepository('CudiBundle\Entity\Stock\Order\Order')
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
        $prefix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.article_barcode_prefix') . $this->getAcademicYear()->getCode(true);

        $form = new AddForm($this->getEntityManager(), $prefix);

        $academicYear = $this->getAcademicYear();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article_id']);

                $item = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Order')
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
                    'cudi_admin_stock_order',
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
            'cudi_admin_stock_order',
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
            'Content-Type'        => 'application/pdf',
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

        $order->setDeliveryDate(\DateTime::createFromFormat('d-m-Y', $this->getParam('date')));
        $this->getEntityManager()->flush();

        $document = new OrderXmlGenerator($this->getEntityManager(), $order);

        $archive = new TmpFile();
        $document->generateArchive($archive);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition'        => 'attachment; filename="order.zip"',
            'Content-Type'               => 'application/zip',
            'Content-Length'             => filesize($archive->getFileName()),
        ));
        $this->getResponse()->setHeaders($headers);

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

    private function _search(Period $period)
    {
        switch($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByTitleAndPeriod($this->getParam('string'), $period);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllBySupplierStringAndPeriod($this->getParam('string'), $period);
        }
    }

    private function _getSupplier()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the supplier!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
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
                    'No supplier with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
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
                    'No ID was given to identify the order!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $order = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Order\Order')
            ->findOneById($this->getParam('id'));

        if (null === $order) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No order with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
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
                    'No ID was given to identify the order item!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Order\Item')
            ->findOneById($this->getParam('id'));

        if (null === $item) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No order item with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $item;
    }
}
