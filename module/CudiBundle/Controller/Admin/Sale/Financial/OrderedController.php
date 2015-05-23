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

namespace CudiBundle\Controller\Admin\Sale\Financial;

use CommonBundle\Entity\General\AcademicYear,
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
        $academicYear = $this->getAcademicYearEntity();

        if (null !== $this->getParam('field')) {
            $records = $this->individualSearch($academicYear);
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        $academicYear = $this->getAcademicYearEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $orders = $this->individualSearch($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($orders as $order) {
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

    /**
     * @param  AcademicYear             $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function individualSearch(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByArticleQuery($this->getParam('string'), $academicYear);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllBySupplierQuery($this->getParam('string'), $academicYear);
        }
    }

    public function ordersAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if (null !== $this->getParam('field')) {
            $records = $this->ordersSearch($academicYear);
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Order')
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        $academicYear = $this->getAcademicYearEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $records = $this->ordersSearch($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($records as $order) {
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

    /**
     * @param  AcademicYear             $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function ordersSearch(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Order')
                    ->findAllBySupplierAndAcademicYearQuery($this->getParam('string'), $academicYear);
        }
    }

    public function orderAction()
    {
        if (!($order = $this->getOrderEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if (null !== $this->getParam('field')) {
            $records = $this->orderSearch($order, $academicYear);
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByOrderQuery($order, $academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        if (!($order = $this->getOrderEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $records = $this->orderSearch($order, $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($records as $order) {
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

    /**
     * @param  Order                    $order
     * @param  AcademicYear             $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function orderSearch(Order $order, AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByArticleAndOrderQuery($this->getParam('string'), $order, $academicYear);
        }
    }

    public function suppliersAction()
    {
        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Supplier')
                ->findAllQuery(),
            $this->getParam('page')
        );

        foreach ($paginator as $item) {
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
        if (!($supplier = $this->getSupplierEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        if (null !== $this->getParam('field')) {
            $records = $this->supplierSearch($supplier, $this->getAcademicYearEntity());
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllBySupplierEntityQuery($supplier, $this->getAcademicYearEntity());
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        if (!($supplier = $this->getSupplierEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $records = $this->supplierSearch($supplier, $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($records as $order) {
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

    /**
     * @param  Supplier                 $supplier
     * @param  AcademicYear             $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function supplierSearch(Supplier $supplier, AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                    ->findAllByArticleTitleAndSupplierAndAcademicYearQuery($this->getParam('string'), $supplier, $academicYear);
        }
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
                'cudi_admin_sales_financial_ordered',
                array(
                    'action' => 'suppliers',
                )
            );

            return;
        }

        return $order;
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
                'cudi_admin_sales_financial_ordered',
                array(
                    'action' => 'suppliers',
                )
            );

            return;
        }

        $supplier->setEntityManager($this->getEntityManager());

        return $supplier;
    }
}
