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
    CudiBundle\Entity\Sale\SaleItem\Prof as ProfItem,
    CudiBundle\Entity\Sale\SaleItem\External as ExternalItem,
    CudiBundle\Entity\Sale\Session,
    CudiBundle\Entity\Supplier,
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
            $records = $this->_individualSearch($academicYear);

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        $records = $this->_individualSearch($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceOf ProfItem || $soldItem instanceOf ExternalItem)
                $organization = null;
            else
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
            $item->type = ucfirst($soldItem->getType());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _individualSearch(AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'type':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByTypeAndAcademicYearQuery($this->getParam('string'), $academicYear);
        }
    }

    public function sessionsAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findAllByAcademicYearQuery($academicYear),
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
            $records = $this->_sessionSearch($session);

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllBySessionQuery($session);
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        $records = $this->_sessionSearch($session)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceOf ProfItem || $soldItem instanceOf ExternalItem)
                $organization = null;
            else
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
            $item->type = ucfirst($soldItem->getType());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _sessionSearch(Session $session)
    {
        switch($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndSessionQuery($this->getParam('string'), $session);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndSessionQuery($this->getParam('string'), $session);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndSessionQuery($this->getParam('string'), $session);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndSessionQuery($this->getParam('string'), $session);
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
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
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

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->_articlesSearch($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

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
            $records = $this->_articleSearch($article, $this->getAcademicYear());

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllByArticleEntityQuery($article, $this->getAcademicYear());
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        $records = $this->_articleSearch($article, $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceOf ProfItem || $soldItem instanceOf ExternalItem)
                $organization = null;
            else
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
            $item->type = ucfirst($soldItem->getType());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _articleSearch(Article $article, AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndArticleQuery($this->getParam('string'), $article, $academicYear);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndArticleQuery($this->getParam('string'), $article, $academicYear);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndArticleQuery($this->getParam('string'), $article, $academicYear);
        }
    }

    public function suppliersAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Supplier')
                ->findAllQuery(),
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
            $records = $this->_supplierSearch($supplier, $this->getAcademicYear());

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllBySupplierEntityQuery($supplier, $this->getAcademicYear());
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
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

        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $records = $this->_supplierSearch($supplier, $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceOf ProfItem || $soldItem instanceOf ExternalItem)
                $organization = null;
            else
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType() ? $soldItem->getDiscountType() : '';
            $item->type = ucfirst($soldItem->getType());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _supplierSearch(Supplier $supplier, AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndSupplierQuery($this->getParam('string'), $supplier, $academicYear);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndSupplierQuery($this->getParam('string'), $supplier, $academicYear);
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndSupplierQuery($this->getParam('string'), $supplier, $academicYear);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndSupplierQuery($this->getParam('string'), $supplier, $academicYear);
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
