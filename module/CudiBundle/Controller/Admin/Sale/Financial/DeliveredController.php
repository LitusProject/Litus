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
    CudiBundle\Entity\Supplier,
    Zend\View\Model\ViewModel;

/**
 * DeliveredController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DeliveredController extends \CudiBundle\Component\Controller\ActionController
{
    public function individualAction()
    {
        $academicYear = $this->getAcademicYear();
        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_individualSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
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
        foreach($records as $delivery) {
            $item = (object) array();
            $item->id = $delivery->getId();
            $item->timestamp = $delivery->getTimestamp()->format('d/m/Y H:i');
            $item->article = $delivery->getArticle()->getMainArticle()->getTitle();
            $item->barcode = $delivery->getArticle()->getBarcode();
            $item->supplier = $delivery->getArticle()->getSupplier()->getName();
            $item->number = $delivery->getNumber();
            $item->price = number_format($delivery->getNumber() * $delivery->getArticle()->getPurchasePrice()/100, 2);
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
                    ->getRepository('CudiBundle\Entity\Stock\Delivery')
                    ->findAllByArticlePaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Delivery')
                    ->findAllBySupplierPaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
        }
    }

    public function articlesAction()
    {
        $academicYear = $this->getAcademicYear();
        if (null !== $this->getParam('field'))
            $records = $this->_articlesSearch($academicYear);

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYear($academicYear);
        }

        $paginator = $this->paginator()->createFromArray(
            $records,
            $this->getParam('page')
        );

        foreach($paginator as $item) {
            $item->setEntityManager($this->getEntityManager());
        }

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

    public function articlesSearchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        $articles = $this->_articlesSearch($academicYear);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach($articles as $article) {
            $article->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->author = $article->getMainArticle()->getAuthors();
            $item->barcode = $article->getBarcode();
            $item->publishers = $article->getMainArticle()->getPublishers();
            $item->purchasePrice = number_format($article->getPurchasePrice()/100, 2);
            $item->sellPrice = number_format($article->getSellPrice()/100, 2);
            $item->numberDelivered = $article->getNumberDelivered($academicYear);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _articlesSearch(AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByTitleAndAcademicYear($this->getParam('string'), $academicYear);
            case 'author':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByAuthorAndAcademicYear($this->getParam('string'), $academicYear);
            case 'publisher':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByPublisherAndAcademicYear($this->getParam('string'), $academicYear);
            case 'barcode':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByBarcodeAndAcademicYear($this->getParam('string'), $academicYear);
        }
    }

    public function articleAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        list($records, $totalNumber) = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Delivery')
            ->findAllByArticleEntityPaginator($article, $this->getParam('page'), $this->paginator()->getItemsPerPage(), $this->getAcademicYear());

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        return new ViewModel(
            array(
                'article' => $article,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function suppliersAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Supplier',
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
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
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
        foreach($records as $delivery) {
            $item = (object) array();
            $item->id = $delivery->getId();
            $item->timestamp = $delivery->getTimestamp()->format('d/m/Y H:i');
            $item->article = $delivery->getArticle()->getMainArticle()->getTitle();
            $item->barcode = $delivery->getArticle()->getBarcode();
            $item->number = $delivery->getNumber();
            $item->price = number_format($delivery->getNumber() * $delivery->getArticle()->getPurchasePrice()/100, 2);
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
                    ->getRepository('CudiBundle\Entity\Stock\Delivery')
                    ->findAllByArticleTitleAndSupplierAndAcademicYear($this->getParam('string'), $page, $numberRecords, $supplier, $academicYear);
        }
    }

    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_financial_delivered',
                array(
                    'action' => 'articles'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_financial_delivered',
                array(
                    'action' => 'articles'
                )
            );

            return;
        }

        $article->setEntityManager($this->getEntityManager());

        return $article;
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
                'cudi_admin_sales_financial_delivered',
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
                'cudi_admin_sales_financial_delivered',
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