<?php

namespace CudiBundle\Controller\Admin\Sale\Financial;

use CommonBundle\Entity\General\AcademicYear;
use CudiBundle\Entity\Sale\Article as SaleArticle;
use CudiBundle\Entity\Supplier;
use Laminas\View\Model\ViewModel;

/**
 * DeliveredController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DeliveredController extends \CudiBundle\Component\Controller\ActionController
{
    public function individualAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($this->getParam('field') !== null) {
            $records = $this->individualSearch($academicYear);
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
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
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'academicYears'      => $academicYears,
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

        $deliveries = $this->individualSearch($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($deliveries as $delivery) {
            $item = (object) array();
            $item->id = $delivery->getId();
            $item->timestamp = $delivery->getTimestamp()->format('d/m/Y H:i');
            $item->article = $delivery->getArticle()->getMainArticle()->getTitle();
            $item->barcode = $delivery->getArticle()->getBarcode();
            $item->supplier = $delivery->getArticle()->getSupplier()->getName();
            $item->number = $delivery->getNumber();
            $item->price = number_format($delivery->getNumber() * $delivery->getArticle()->getPurchasePrice() / 100, 2);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function individualSearch(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Delivery')
                    ->findAllByArticleAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Delivery')
                    ->findAllBySupplierAndAcademicYearQuery($this->getParam('string'), $academicYear);
        }
    }

    public function articlesAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($this->getParam('field') !== null) {
            $records = $this->articlesSearch($academicYear);
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
        );

        foreach ($paginator as $item) {
            $item->setEntityManager($this->getEntityManager());
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'academicYears'      => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function articlesSearchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYearEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->articlesSearch($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            $article->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->author = $article->getMainArticle()->getAuthors();
            $item->barcode = $article->getBarcode();
            $item->publishers = $article->getMainArticle()->getPublishers();
            $item->purchasePrice = number_format($article->getPurchasePrice() / 100, 2);
            $item->sellPrice = number_format($article->getSellPrice() / 100, 2);
            $item->numberDelivered = $article->getNumberDelivered($academicYear);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function articlesSearch(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'author':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByAuthorAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'publisher':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByPublisherAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'barcode':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByBarcodeAndAcademicYearQuery($this->getParam('string'), $academicYear);
        }
    }

    public function articleAction()
    {
        $article = $this->getSaleArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
                ->findAllByArticleEntityQuery($article, $this->getAcademicYearEntity()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'article'            => $article,
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'academicYears'      => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
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
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'academicYears'      => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function supplierAction()
    {
        $supplier = $this->getSupplierEntity();
        if ($supplier === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        if ($this->getParam('field') !== null) {
            $records = $this->supplierSearch($supplier, $this->getAcademicYearEntity());
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
                ->findAllBySupplierEntityQuery($supplier, $this->getAcademicYearEntity());
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'supplier'           => $supplier,
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'academicYears'      => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function supplierSearchAction()
    {
        $this->initAjax();

        $supplier = $this->getSupplierEntity();
        if ($supplier === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $deliveries = $this->supplierSearch($supplier, $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($deliveries as $delivery) {
            $item = (object) array();
            $item->id = $delivery->getId();
            $item->timestamp = $delivery->getTimestamp()->format('d/m/Y H:i');
            $item->article = $delivery->getArticle()->getMainArticle()->getTitle();
            $item->barcode = $delivery->getArticle()->getBarcode();
            $item->number = $delivery->getNumber();
            $item->price = number_format($delivery->getNumber() * $delivery->getArticle()->getPurchasePrice() / 100, 2);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @param  Supplier     $supplier
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function supplierSearch(Supplier $supplier, AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Delivery')
                    ->findAllByArticleTitleAndSupplierAndAcademicYearQuery($this->getParam('string'), $supplier, $academicYear);
        }
    }

    /**
     * @return SaleArticle|null
     */
    private function getSaleArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Sale\Article');

        if (!($article instanceof SaleArticle)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_financial_delivered',
                array(
                    'action' => 'articles',
                )
            );

            return;
        }

        $article->setEntityManager($this->getEntityManager());

        return $article;
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
                'cudi_admin_sales_financial_delivered',
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
