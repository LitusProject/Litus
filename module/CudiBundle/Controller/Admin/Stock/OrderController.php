<?php

namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\Acl\Driver\Exception\RuntimeException;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use CudiBundle\Component\Document\Generator\Order\Pdf as OrderPdfGenerator;
use CudiBundle\Component\Document\Generator\Order\Xml as OrderXmlGenerator;
use CudiBundle\Entity\Stock\Delivery;
use CudiBundle\Entity\Stock\Order;
use CudiBundle\Entity\Stock\Order\Item as OrderItem;
use CudiBundle\Entity\Stock\Period;
use CudiBundle\Entity\Supplier;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * OrderController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OrderController extends \CudiBundle\Component\Controller\ActionController
{
    const NOT_APPLICABLE = '/';

    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Supplier')
                ->findAllQuery(),
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers'         => $suppliers,
            )
        );
    }

    public function overviewAction()
    {
        $period = $this->getActiveStockPeriodEntity();
        if ($period === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
            $orders = $this->search($period);
        }

        if (!isset($orders)) {
            $orders = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByPeriodQuery($period);
        }

        $paginator = $this->paginator()->createFromQuery(
            $orders,
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers'         => $suppliers,
                'period'            => $period,
            )
        );
    }

    public function searchAction()
    {
        $period = $this->getActiveStockPeriodEntity();
        if ($period === null) {
            return new ViewModel();
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $orders = $this->search($period)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($orders as $order) {
            $item = (object)array();
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
        $supplier = $this->getSupplierEntity();
        if ($supplier === null) {
            return new ViewModel();
        }

        $period = $this->getActiveStockPeriodEntity();
        if ($period === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order')
                ->findAllBySupplierAndPeriodQuery($supplier, $period),
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'supplier'          => $supplier,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'suppliers'         => $suppliers,
            )
        );
    }

    public function editAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        $form = $this->getForm(
            'cudi_stock_order_comment',
            array(
                'order' => $order,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $order->setComment($formData['comment']);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The order item was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_stock_order',
                    array(
                        'action' => 'edit',
                        'id'     => $order->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'order'     => $order,
                'supplier'  => $order->getSupplier(),
                'suppliers' => $suppliers,
                'form'      => $form,
            )
        );
    }

    public function addAction()
    {
        $prefix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.article_barcode_prefix') . $this->getAcademicYearEntity()->getCode(true);

        $form = $this->getForm(
            'cudi_stock_order_add',
            array(
                'barcode_prefix' => $prefix,
            )
        );

        $academicYear = $this->getAcademicYearEntity();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article']['id']);

                $item = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order')
                    ->addNumberByArticle($article, $formData['number'], $this->getAuthentication()->getPersonObject());

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The order item was successfully added!'
                );

                return $this->redirect()->toRoute(
                    'cudi_admin_stock_order',
                    array(
                        'action' => 'edit',
                        'id'     => $item->getOrder()->getId(),
                    )
                );

//                return new ViewModel(
//                    array(
//                        'currentAcademicYear' => $academicYear,
//                    )
//                );
            }
        }

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'form'                => $form,
                'suppliers'           => $suppliers,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function editItemAction()
    {
        $item = $this->getOrderItemEntity();
        if ($item === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'cudi_stock_order_edit',
            array(
                'item' => $item,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $item->setNumber($formData['number']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The order item was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_stock_order',
                    array(
                        'action' => 'edit',
                        'id'     => $item->getOrder()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'order'     => $item->getOrder(),
                'item'      => $item,
                'form'      => $form,
                'suppliers' => $suppliers,
                'supplier'  => $item->getOrder()->getSupplier(),
            )
        );
    }

    public function deleteAllAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $items = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Order\Item')
            ->findAllByOrderOnAlpha($order);

        foreach ($items as $item) {
            $this->getEntityManager()->remove($item);
        }
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'All the order items were successfully removed!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_stock_order',
            array(
                'action' => 'edit',
                'id'     => $order->getId(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $item = $this->getOrderItemEntity();
        if ($item === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($item);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object)array('status' => 'success'),
            )
        );
    }

    public function placeAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $order->setOrdered();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The order is successfully placed!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_stock_order',
            array(
                'action' => 'edit',
                'id'     => $order->getId(),
            )
        );

        return new ViewModel();
    }

    public function pdfAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $file = new TmpFile();
        $document = new OrderPdfGenerator($this->getEntityManager(), $order, $this->getParam('order'), $file);
        $document->generate();

        $filename = 'order_' . $order->getDateOrdered()->format('Ymd') . '.pdf';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename=' . $filename,
                'Content-Type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function csvAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $sortOrder = $this->getParam('order');
        $items = null;
        switch ($sortOrder) {
            case 'barcode':
                $items = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByOrderOnBarcode($order);
                break;
            case 'alpha':
                $items = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByOrderOnAlpha($order);
                break;
            default:
                new RuntimeException('Unknown sorting order');
        }

        $file = new CsvFile();

        $heading = array('Barcode', 'Title', 'RV', 'Binding', 'Color', '# Pages', 'Amount', 'Isbn', 'Author', 'Publisher');

        $results = array();
        foreach ($items as $item) {
            if ($item->getArticle()->getMainArticle()->isInternal()) {
                $results[] = array(
                    (string)$item->getArticle()->getBarcode(),
                    $item->getArticle()->getMainArticle()->getTitle(),
                    $item->getArticle()->getMainArticle()->isRectoVerso() ? '1' : '0',
                    $item->getArticle()->getMainArticle()->getBinding()->getName(),
                    $item->getArticle()->getMainArticle()->isColored() ? '1' : '0',
                    (string)($item->getArticle()->getMainArticle()->getNbBlackAndWhite() + $item->getArticle()->getMainArticle()->getNbColored()),
                    (string)$item->getNumber(),
                    OrderController::NOT_APPLICABLE,
                    OrderController::NOT_APPLICABLE,
                    OrderController::NOT_APPLICABLE,
                );
            } else {
                $results[] = array(
                    OrderController::NOT_APPLICABLE,
                    $item->getArticle()->getMainArticle()->getTitle(),
                    OrderController::NOT_APPLICABLE,
                    OrderController::NOT_APPLICABLE,
                    OrderController::NOT_APPLICABLE,
                    OrderController::NOT_APPLICABLE,
                    (string)$item->getNumber(),
                    (string)$item->getArticle()->getMainArticle()->getIsbn(),
                    $item->getArticle()->getMainArticle()->getAuthors(),
                    $item->getArticle()->getMainArticle()->getPublishers(),
                );
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $filename = 'order ' . $order->getDateOrdered()->format('Ymd') . '.csv';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename=' . $filename,
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

    public function exportAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $date = $this->getParamDate();
        if ($date === null) {
            return new ViewModel();
        }

        $order->setDeliveryDate($date);
        $this->getEntityManager()->flush();

        $document = new OrderXmlGenerator($this->getEntityManager(), $order);

        $archive = new TmpFile();
        $document->generateArchive($archive);

        if (filesize($archive->getFileName()) == 0) {
            $this->flashMessenger()->notice(
                'NOTICE',
                'The order did not contain any exportable items, which means none of its articles were internal!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
                array(
                    'action' => 'edit',
                    'id'     => $order->getId(),
                )
            );

            return new ViewModel();
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="order.zip"',
                'Content-Type'        => 'application/zip',
                'Content-Length'      => filesize($archive->getFileName()),
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $archive->getContent(),
            )
        );
    }

    public function cancelAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $order->setCanceled();
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The order was successfully canceled!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_stock_order',
            array(
                'action' => 'edit',
                'id'     => $order->getId(),
            )
        );

        return new ViewModel();
    }

    public function deliveredAction()
    {
        $order = $this->getOrderEntity();
        $sortby = $this->getParam('order', 'barcode');

        $form = $this->getForm('cudi_stock_order_set-delivered', array('order' => $order, 'sortby' => $sortby));

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                foreach ($formData['articles'] as $id => $amount) {
                    $article = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($id);

                    $item = new Delivery($article, $amount, $this->getAuthentication()->getPersonObject());
                    $this->getEntityManager()->persist($item);
                    $this->getEntityManager()->flush();

                    $enableAssignment = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.enable_automatic_assignment') &&
                        $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.enable_assign_after_stock_update');

                    if ($enableAssignment) {
                        $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Booking')
                            ->assignAllByArticle($article, $this->getMailTransport());
                        $this->getEntityManager()->flush();
                    }
                }

                $order->setDelivered(true);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The delivery was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_stock_order',
                    array(
                        'action' => 'edit',
                        'id'     => $order->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'supplier'  => $order->getSupplier(),
                'suppliers' => $suppliers,
                'form'      => $form,
                'order'     => $order,
                'sortby'    => $sortby,
            )
        );
    }

    /**
     * @param Period $period
     * @return \Doctrine\ORM\Query|null
     */
    private function search(Period $period)
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByTitleAndPeriodQuery($this->getParam('string'), $period);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllBySupplierStringAndPeriodQuery($this->getParam('string'), $period);
        }
    }

    /**
     * @return Supplier|null
     */
    private function getSupplierEntity()
    {
        $supplier = $this->getEntityById('CudiBundle\Entity\Supplier');

        if (!($supplier instanceof Supplier)) {
            $this->flashMessenger()->error(
                'Error',
                'No supplier was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $supplier;
    }

    /**
     * @return Order|null
     */
    private function getOrderEntity()
    {
        $order = $this->getEntityById('CudiBundle\Entity\Stock\Order');

        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No order was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $order;
    }

    /**
     * @return OrderItem|null
     */
    private function getOrderItemEntity()
    {
        $item = $this->getEntityById('CudiBundle\Entity\Stock\Order\Item');

        if (!($item instanceof OrderItem)) {
            $this->flashMessenger()->error(
                'Error',
                'No order item was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $item;
    }

    /**
     * @return DateTime|null
     */
    private function getParamDate()
    {
        $date = DateTime::createFromFormat('d-m-Y', $this->getParam('date'));

        if ($date instanceof DateTime) {
            return $date;
        }
    }
}
