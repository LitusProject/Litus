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

namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\Util\File\TmpFile,
    CudiBundle\Component\Document\Generator\Order\Pdf as OrderPdfGenerator,
    CudiBundle\Component\Document\Generator\Order\Xml as OrderXmlGenerator,
    CudiBundle\Entity\Stock\Order\Item as OrderItem,
    CudiBundle\Entity\Stock\Order\Order,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Supplier,
    DateTime,
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
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
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
        if (!($period = $this->getActiveStockPeriodEntity())) {
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
        if (!($supplier = $this->getSupplierEntity())) {
            return new ViewModel();
        }

        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Order')
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
        if (!($order = $this->getOrderEntity())) {
            return new ViewModel();
        }

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        $form = $this->getForm('cudi_stock_order_comment', array(
            'order' => $order,
        ));

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

        $form = $this->getForm('cudi_stock_order_add', array(
            'barcode_prefix' => $prefix,
        ));

        $academicYear = $this->getAcademicYearEntity();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article']['id']);

                $item = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Order')
                    ->addNumberByArticle($article, $formData['number'], $this->getAuthentication()->getPersonObject());

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
                'form'                => $form,
                'suppliers'           => $suppliers,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function editItemAction()
    {
        if (!($item = $this->getOrderItemEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_stock_order_edit', array(
            'item' => $item,
        ));

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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($item = $this->getOrderItemEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($item);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function placeAction()
    {
        if (!($order = $this->getOrderEntity())) {
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
        if (!($order = $this->getOrderEntity())) {
            return new ViewModel();
        }

        $file = new TmpFile();
        $document = new OrderPdfGenerator($this->getEntityManager(), $order, $this->getParam('order'), $file);
        $document->generate();

        $filename = 'order ' . $order->getDateOrdered()->format('Ymd') . '.pdf';

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename=' . $filename,
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
        if (!($order = $this->getOrderEntity()) || !($date = $this->getParamDate())) {
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
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="order.zip"',
            'Content-Type'        => 'application/zip',
            'Content-Length'      => filesize($archive->getFileName()),
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $archive->getContent(),
            )
        );
    }

    public function cancelAction()
    {
        if (!($order = $this->getOrderEntity())) {
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

    /**
     * @param  Period                   $period
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
        $order = $this->getEntityById('CudiBundle\Entity\Stock\Order\Order');

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
