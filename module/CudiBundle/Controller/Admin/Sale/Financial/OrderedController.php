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

namespace CudiBundle\Controller\Admin\Sale\Financial;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Stock\Order\Order,
    CudiBundle\Entity\Supplier,
    Zend\View\Model\ViewModel;

/**
 * OrderedController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OrderedController extends \CudiBundle\Component\Controller\ActionController
{
    public function individualAction()
    {
        $academicYear = $this->getAcademicYear();
        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_individualSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllPaginator($this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function individualSearchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_individualSearch(0, $numResults, $academicYear);

        $result = array();
        foreach($records as $order) {
            $item = (object) array();
            $item->id = $order->getId();
            $item->dateOrdered = $order->getOrder()->getDateOrdered()->format('d/m/Y H:i');
            $item->article = $order->getArticle()->getMainArticle()->getTitle();
            $item->barcode = $order->getArticle()->getBarcode();
            $item->supplier = $order->getArticle()->getSupplier()->getName();
            $item->number = $order->getNumber();
            $item->price = number_format($order->getNumber() * $order->getArticle()->getPurchasePrice()/100, 2);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _individualSearch($page, $numberRecords, AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByArticlePaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllBySupplierPaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
        }
    }

    public function ordersAction()
    {
        $academicYear = $this->getAcademicYear();
        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_ordersSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Order')
                ->findAllPaginator($this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function ordersSearchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_ordersSearch(0, $numResults, $academicYear);

        $result = array();
        foreach($records as $order) {
            $item = (object) array();
            $item->id = $order->getId();
            $item->dateOrdered = $order->getDateOrdered()->format('d/m/Y H:i');
            $item->person = $order->getPerson()->getFullName();
            $item->supplier = $order->getSupplier()->getName();
            $item->number = $order->getTotalNumber();
            $item->price = number_format($order->getPrice()/100, 2);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _ordersSearch($page, $numberRecords, AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Order')
                    ->findAllBySupplierPaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
        }
    }

    public function orderAction()
    {
        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();
        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_orderSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $order, $academicYear);

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByOrderPaginator($order, $this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'order' => $order,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function orderSearchAction()
    {
        $this->initAjax();

        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_orderSearch(0, $numResults, $order, $academicYear);

        $result = array();
        foreach($records as $order) {
            $item = (object) array();
            $item->id = $order->getId();
            $item->article = $order->getArticle()->getMainArticle()->getTitle();
            $item->barcode = $order->getArticle()->getBarcode();
            $item->number = $order->getNumber();
            $item->price = number_format($order->getNumber() * $order->getArticle()->getPurchasePrice()/100, 2);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _orderSearch($page, $numberRecords, Order $order, AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByArticleAndOrderPaginator($this->getParam('string'), $order, $page, $numberRecords, $academicYear);
        }
    }

    public function suppliersAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Supplier')
                ->findAll(),
            $this->getParam('page')
        );

        foreach($paginator as $item) {
            $item->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function supplierAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_supplierSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $supplier, $this->getAcademicYear());

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllBySupplierEntityPaginator($supplier, $this->getParam('page'), $this->paginator()->getItemsPerPage(), $this->getAcademicYear());
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        return new ViewModel(
            array(
                'supplier' => $supplier,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function supplierSearchAction()
    {
        $this->initAjax();

        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_supplierSearch(0, $numResults, $supplier, $academicYear);

        $result = array();
        foreach($records as $order) {
            $item = (object) array();
            $item->id = $order->getId();
            $item->timestamp = $order->getOrder()->getDateOrdered()->format('d/m/Y H:i');
            $item->article = $order->getArticle()->getMainArticle()->getTitle();
            $item->barcode = $order->getArticle()->getBarcode();
            $item->number = $order->getNumber();
            $item->price = number_format($order->getNumber() * $order->getArticle()->getPurchasePrice()/100, 2);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _supplierSearch($page, $numberRecords, Supplier $supplier, AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByArticleTitleAndSupplierAndAcademicYear($this->getParam('string'), $page, $numberRecords, $supplier, $academicYear);
        }
    }

    public function articlesAction()
    {
        return new ViewModel();
    }

    public function articleAction()
    {
        return new ViewModel();
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
                'cudi_admin_sales_financial_ordered',
                array(
                    'action' => 'orders'
                )
            );

            return;
        }

        $orders = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Order\Order')
            ->findOneById($this->getParam('id'));

        if (null === $orders) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No order with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_financial_ordered',
                array(
                    'action' => 'orders'
                )
            );

            return;
        }

        return $orders;
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
                'cudi_admin_sales_financial_ordered',
                array(
                    'action' => 'suppliers'
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
                'cudi_admin_sales_financial_ordered',
                array(
                    'action' => 'suppliers'
                )
            );

            return;
        }

        $supplier->setEntityManager($this->getEntityManager());

        return $supplier;
    }
}
