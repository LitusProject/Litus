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

namespace CudiBundle\Controller\Admin\Sale\Financial;

use CommonBundle\Entity\General\AcademicYear;
use CudiBundle\Entity\Sale\Article as SaleArticle;
use CudiBundle\Entity\Sale\SaleItem\External as ExternalItem;
use CudiBundle\Entity\Sale\SaleItem\Prof as ProfItem;
use CudiBundle\Entity\Sale\Session;
use CudiBundle\Entity\Supplier;
use Zend\View\Model\ViewModel;

/**
 * SoldController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SoldController extends \CudiBundle\Component\Controller\ActionController
{
    public function individualAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if (null !== $this->getParam('field')) {
            $records = $this->individualSearch($academicYear);
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
        );

        foreach ($paginator as $item) {
            $item->getSession()->setEntityManager($this->getEntityManager());
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        return new ViewModel(
            array(
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'academicYears'      => $academicYears,
                'activeAcademicYear' => $academicYear,
                'organizations'      => $organizations,
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

        $records = $this->individualSearch($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceof ProfItem || $soldItem instanceof ExternalItem) {
                $organization = null;
            } else {
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());
            }

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice() / 100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice() / 100, 2);
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

    /**
     * @param  AcademicYear             $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function individualSearch(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndAcademicYearQuery($this->getParam('string'), $academicYear);
            case 'organization':
                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById(substr($this->getParam('string'), strlen('organization-')));

                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findallByAcademicYearAndOrganizationQuery($academicYear, $organization);
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
        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findAllByAcademicYearQuery($academicYear),
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

    public function sessionAction()
    {
        if (!($session = $this->getSessionEntity())) {
            return new ViewModel();
        }

        $academicYear = $session->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if (null !== $this->getParam('field')) {
            $records = $this->sessionSearch($session);
        }

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
                'session'                  => $session,
                'organizations'            => $organizations,
                'paginator'                => $paginator,
                'paginationControl'        => $this->paginator()->createControl(true),
                'academicYears'            => $academicYears,
                'activeAcademicYear'       => $academicYear,
                'otherOrganizationEnabled' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.enable_other_organization'),
            )
        );
    }

    public function sessionSearchAction()
    {
        $this->initAjax();

        if (!($session = $this->getSessionEntity())) {
            return new ViewModel();
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $records = $this->sessionSearch($session)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceof ProfItem || $soldItem instanceof ExternalItem) {
                $organization = null;
            } else {
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());
            }

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice() / 100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice() / 100, 2);
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

    /**
     * @param  Session                  $session
     * @return \Doctrine\ORM\Query|null
     */
    private function sessionSearch(Session $session)
    {
        switch ($this->getParam('field')) {
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndSessionQuery($this->getParam('string'), $session);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndSessionQuery($this->getParam('string'), $session);
            case 'organization':
                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById(substr($this->getParam('string'), strlen('organization-')));

                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllBySessionAndOrganizationQuery($session, $organization);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndSessionQuery($this->getParam('string'), $session);
        }
    }

    public function articlesAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if (null !== $this->getParam('field')) {
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
            $item->numberSold = $article->getNumberSold($academicYear);
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
        if (!($article = $this->getSaleArticleEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if (null !== $this->getParam('field')) {
            $records = $this->articleSearch($article, $this->getAcademicYearEntity());
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllByArticleEntityQuery($article, $this->getAcademicYearEntity());
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
        );

        foreach ($paginator as $item) {
            $item->getSession()->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'article'                  => $article,
                'organizations'            => $organizations,
                'paginator'                => $paginator,
                'paginationControl'        => $this->paginator()->createControl(true),
                'academicYears'            => $academicYears,
                'activeAcademicYear'       => $academicYear,
                'otherOrganizationEnabled' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.enable_other_organization'),
            )
        );
    }

    public function articleSearchAction()
    {
        $this->initAjax();

        if (!($article = $this->getSaleArticleEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $records = $this->articleSearch($article, $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceof ProfItem || $soldItem instanceof ExternalItem) {
                $organization = null;
            } else {
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());
            }

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice() / 100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice() / 100, 2);
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

    /**
     * @param  SaleArticle              $article
     * @param  AcademicYear             $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function articleSearch(SaleArticle $article, AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndArticleQuery($this->getParam('string'), $article, $academicYear);
            case 'organization':
                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById(substr($this->getParam('string'), strlen('organization-')));

                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndArticleQuery($article, $academicYear, $organization);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndArticleQuery($this->getParam('string'), $article, $academicYear);
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
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'academicYears'      => $academicYears,
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

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if (null !== $this->getParam('field')) {
            $records = $this->supplierSearch($supplier, $this->getAcademicYearEntity());
        }

        if (!isset($records)) {
            $records = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllBySupplierEntityQuery($supplier, $this->getAcademicYearEntity());
        }

        $paginator = $this->paginator()->createFromQuery(
            $records,
            $this->getParam('page')
        );

        foreach ($paginator as $item) {
            $item->getSession()->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'supplier'                 => $supplier,
                'organizations'            => $organizations,
                'paginator'                => $paginator,
                'paginationControl'        => $this->paginator()->createControl(true),
                'academicYears'            => $academicYears,
                'activeAcademicYear'       => $academicYear,
                'otherOrganizationEnabled' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.enable_other_organization'),
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
        foreach ($records as $soldItem) {
            $soldItem->getSession()->setEntityManager($this->getEntityManager());

            if ($soldItem instanceof ProfItem || $soldItem instanceof ExternalItem) {
                $organization = null;
            } else {
                $organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear());
            }

            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getType() == 'regular' ? $soldItem->getPerson()->getFullName() : $soldItem->getPerson();
            $item->organization = $organization ? $organization->getName() : '';
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice() / 100, 2);
            $item->purchasePrice = number_format($soldItem->getArticle()->getPurchasePrice() / 100, 2);
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
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByArticleAndSupplierQuery($this->getParam('string'), $supplier, $academicYear);
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByPersonAndSupplierQuery($this->getParam('string'), $supplier, $academicYear);
            case 'organization':
                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById(substr($this->getParam('string'), strlen('organization-')));

                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByOrganizationAndSupplierQuery($supplier, $academicYear, $organization);
            case 'discount':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findAllByDiscountAndSupplierQuery($this->getParam('string'), $supplier, $academicYear);
        }
    }

    /**
     * @return Session|null
     */
    private function getSessionEntity()
    {
        $session = $this->getEntityById('CudiBundle\Entity\Sale\Session');

        if (!($session instanceof Session)) {
            $this->flashMessenger()->error(
                'Error',
                'No session was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_financial_sold',
                array(
                    'action' => 'sessions',
                )
            );

            return;
        }

        $session->setEntityManager($this->getEntityManager());

        return $session;
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
                'cudi_admin_sales_financial_sold',
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
                'cudi_admin_sales_financial_sold',
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
