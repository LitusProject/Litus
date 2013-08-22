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
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Session,
    Zend\View\Model\ViewModel;

/**
 * SoldController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SoldController extends \CudiBundle\Component\Controller\ActionController
{
    public function individualAction()
    {
        $academicYear = $this->getAcademicYear();
        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_individualSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllPaginator($this->getParam('page'), $this->paginator()->getItemsPerPage(), $academicYear);
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        foreach($paginator as $item) {
            $item->getSession()->setEntityManager($this->getEntityManager());
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

    public function individualSearchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_individualSearch(0, $numResults, $academicYear);

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getPerson()->getFullName();
            $item->organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear())->getName();
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
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
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticlePaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonPaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationPaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountPaginator($this->getParam('string'), $page, $numberRecords, $academicYear);
        }
    }

    public function sessionsAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findAllByAcademicYear($academicYear),
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

    public function sessionAction()
    {
        if (!($session = $this->_getSession()))
            return new ViewModel();

        $academicYear = $session->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_sessionSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $session);

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllBySessionPaginator($session, $this->getParam('page'), $this->paginator()->getItemsPerPage());
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        return new ViewModel(
            array(
                'session' => $session,
                'organizations' => $organizations,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function sessionSearchAction()
    {
        $this->initAjax();

        if (!($session = $this->_getSession()))
            return new ViewModel();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_sessionSearch(0, $numResults, $session);

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getPerson()->getFullName();
            $item->organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear())->getName();
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _sessionSearch($page, $numberRecords, Session $session)
    {
        switch($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndSessionPaginator($this->getParam('string'), $session, $page, $numberRecords);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndSessionPaginator($this->getParam('string'), $session, $page, $numberRecords);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndSessionPaginator($this->getParam('string'), $session, $page, $numberRecords);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndSessionPaginator($this->getParam('string'), $session, $page, $numberRecords);
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
            $item->numberSold = $article->getNumberSold($academicYear);
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

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_articleSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $article, $this->getAcademicYear());

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllByArticleEntityPaginator($article, $this->getParam('page'), $this->paginator()->getItemsPerPage(), $this->getAcademicYear());
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        foreach($paginator as $item) {
            $item->getSession()->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'article' => $article,
                'organizations' => $organizations,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }

    public function articleSearchAction()
    {
        $this->initAjax();

        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_articleSearch(0, $numResults, $article, $academicYear);

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->person = $soldItem->getPerson()->getFullName();
            $item->organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear())->getName();
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _articleSearch($page, $numberRecords, Article $article, AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndArticlePaginator($this->getParam('string'), $article, $page, $numberRecords, $academicYear);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndArticlePaginator($this->getParam('string'), $article, $page, $numberRecords, $academicYear);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndArticlePaginator($this->getParam('string'), $article, $page, $numberRecords, $academicYear);
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

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_supplierSearch($this->getParam('page'), $this->paginator()->getItemsPerPage(), $supplier, $this->getAcademicYear());

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllBySupplierEntityPaginator($supplier, $this->getParam('page'), $this->paginator()->getItemsPerPage(), $this->getAcademicYear());
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        foreach($paginator as $item) {
            $item->getSession()->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'supplier' => $supplier,
                'organizations' => $organizations,
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

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_individualSearch(0, $numResults, $academicYear);

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getPerson()->getFullName();
            $item->organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear())->getName();
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
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
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndSupplierPaginator($this->getParam('string'), $supplier, $page, $numberRecords, $academicYear);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndSupplierPaginator($this->getParam('string'), $supplier, $page, $numberRecords, $academicYear);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndSupplierPaginator($this->getParam('string'), $supplier, $page, $numberRecords, $academicYear);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndSupplierPaginator($this->getParam('string'), $supplier, $page, $numberRecords, $academicYear);
        }
    }

    private function _getSession()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the session!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_financial_sold',
                array(
                    'action' => 'sessions'
                )
            );

            return;
        }

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($this->getParam('id'));

        if (null === $session) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No session with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_financial_sold',
                array(
                    'action' => 'sessions'
                )
            );

            return;
        }

        $session->setEntityManager($this->getEntityManager());

        return $session;
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
                'cudi_admin_sales_financial_sold',
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
                'cudi_admin_sales_financial_sold',
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
                'cudi_admin_sales_financial_sold',
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
                'cudi_admin_sales_financial_sold',
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
