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

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Component\Document\Generator\SaleArticles as SaleArticlesGenerator,
    CudiBundle\Entity\Article\Internal as InternalArticle,
    CudiBundle\Entity\Log\Article\Sale\Bookable as BookableLog,
    CudiBundle\Entity\Log\Article\Sale\Unbookable as UnbookableLog,
    CudiBundle\Entity\Log\Sale\Cancellations as LogCancellations,
    CudiBundle\Entity\Sale\Article as SaleArticle,
    CudiBundle\Entity\Sale\Article\History,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYear();
        $semester = $this->_getSemester();

        if (null !== $this->getParam('field')) {
            $articles = $this->_search($academicYear, $semester);
        }

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYearQuery($academicYear, $semester);
        }

        $paginator = $this->paginator()->createFromQuery(
            $articles,
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'currentSemester' => $semester,
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function exportAction()
    {
        $form = $this->getForm('cudi_sale_article_export');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'cudi_admin_sales_article', array('action' => 'download')
            )
        );

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function downloadAction()
    {
        $form = $this->getForm('cudi_sale_article_export');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $academicYear = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\AcademicYear')
                    ->findOneById($formData['academic_year']);

                $semester = $formData['semester'];

                $file = new CsvFile();
                $document = new SaleArticlesGenerator($this->getEntityManager(), $academicYear, $formData['semester']);
                $document->generateDocument($file);

                $this->getResponse()->getHeaders()
                    ->addHeaders(
                    array(
                        'Content-Disposition' => 'attachment; filename="sale_articles_' . $semester . '_' . $academicYear->getCode() . '.csv"',
                        'Content-Type' => 'text/csv',
                    )
                );

                return new ViewModel(
                    array(
                        'data' => $file->getContent(),
                    )
                );
            }
        }

        return $this->notFoundAction();
    }

    public function addAction()
    {
        if (!($article = $this->_getArticle())) {
            return new ViewModel();
        }

        $article->setEntityManager($this->getEntityManager());

        $form = $this->getForm('cudi_sale_article_add');

        $precalculatedSellPrice = 0;
        $precalculatedPurchasePrice = 0;

        if ($article instanceof InternalArticle) {
            $precalculatedSellPrice = $article->precalculateSellPrice($this->getEntityManager());
            $precalculatedPurchasePrice = $article->precalculatePurchasePrice($this->getEntityManager());
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();
                $saleArticle = $form->hydrateObject(
                    new SaleArticle($article)
                );

                $this->getEntityManager()->persist($saleArticle);

                if (isset($formData['bookable']) && $formData['bookable']) {
                    $this->getEntityManager()->persist(
                        new BookableLog($this->getAuthentication()->getPersonObject(), $saleArticle)
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The sale article was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
                    array(
                        'action' => 'edit',
                        'id' => $saleArticle->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
                'precalculatedSellPrice' => $precalculatedSellPrice,
                'precalculatedPurchasePrice' => $precalculatedPurchasePrice,
            )
        );
    }

    public function editAction()
    {
        if (!($saleArticle = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_edit', array('article' => $saleArticle));

        $mainArticle = $saleArticle->getMainArticle();

        $precalculatedSellPrice = 0;
        $precalculatedPurchasePrice = 0;

        if ($mainArticle instanceof InternalArticle) {
            $precalculatedSellPrice = $mainArticle->precalculateSellPrice($this->getEntityManager());
            $precalculatedPurchasePrice = $mainArticle->precalculatePurchasePrice($this->getEntityManager());
        }

        // make $history before changing the sale article
        $history = new History($saleArticle);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->getEntityManager()->persist($history);

                if ($mainArticle instanceof InternalArticle) {
                    $cachePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.front_page_cache_dir');

                    if (null !== $mainArticle->getFrontPage() && file_exists($cachePath . '/' . $mainArticle->getFrontPage())) {
                        unlink($cachePath . '/' . $mainArticle->getFrontPage());
                    }

                    $mainArticle->setFrontPage();
                }

                if (isset($formData['bookable']) && $formData['bookable'] && !$history->getPrecursor()->isBookable()) {
                    $this->getEntityManager()->persist(new BookableLog($this->getAuthentication()->getPersonObject(), $saleArticle));
                } elseif (!(isset($formData['bookable']) && $formData['bookable']) && $history->getPrecursor()->isBookable()) {
                    $this->getEntityManager()->persist(new UnbookableLog($this->getAuthentication()->getPersonObject(), $saleArticle));
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The sale article was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
                    array(
                        'action' => 'edit',
                        'id' => $saleArticle->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $saleArticle,
                'precalculatedSellPrice' => $precalculatedSellPrice,
                'precalculatedPurchasePrice' => $precalculatedPurchasePrice,
            )
        );
    }

    public function viewAction()
    {
        if (!($saleArticle = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_view', array('article' => $saleArticle));

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $saleArticle,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($saleArticle = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $saleArticle->setIsHistory(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function assignAllAction()
    {
        if (!($saleArticle = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $counter = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->assignAllByArticle($saleArticle, $this->getMailTransport());

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The article is successfully assigned to ' . $counter . ' persons'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $semester = $this->_getSemester();
        $articles = $this->_search($this->getAcademicYear(), $semester)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->author = $article->getMainArticle()->getAuthors();
            $item->barcode = $article->getBarcode();
            $item->publisher = $article->getMainArticle()->getPublishers();
            $item->sellPrice = number_format($article->getSellPrice()/100, 2);
            $item->stockValue = $article->getStockValue();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function historyAction()
    {
        if (!($article = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $history = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\History')
            ->findByArticle($article);

        return new ViewModel(
            array(
                'history' => $history,
                'current' => $article,
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTitleOrBarcodeAndAcademicYearQuery($this->getParam('string'), $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->value = $article->getMainArticle()->getTitle() . ' - ' . $article->getBarcode();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function mailAction()
    {
        if (!($saleArticle = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_mail');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $persons = array();

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail_name');

                foreach ($formData['to'] as $status) {
                    $bookings = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Booking')
                        ->findAllByStatusAndArticleAndPeriod($status, $saleArticle, $this->getActiveStockPeriod());

                    foreach ($bookings as $booking) {
                        if (isset($persons[$booking->getPerson()->getId()])) {
                            continue;
                        }

                        $persons[$booking->getPerson()->getId()] = true;

                        $mail = new Message();
                        $mail->setBody($formData['message'])
                            ->setFrom($mailAddress, $mailName)
                            ->addTo($booking->getPerson()->getEmail(), $booking->getPerson()->getFullName())
                            ->setSubject($formData['subject']);

                        if ('development' != getenv('APPLICATION_ENV')) {
                            $this->getMailTransport()->send($mail);
                        }
                    }
                }

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The email was successfully send to ' . sizeof($persons) . ' academics!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
                    array(
                        'action' => 'edit',
                        'id' => $saleArticle->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'article' => $saleArticle,
                'form' => $form,
            )
        );
    }

    public function cancelBookingsAction()
    {
        if (!($saleArticle = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllActiveByArticleAndPeriod($saleArticle, $this->getActiveStockPeriod());

        $idsCancelled = array();
        foreach ($bookings as $booking) {
            $booking->setStatus('canceled', $this->getEntityManager());
            $idsCancelled[] = $booking->getId();
        }

        $this->getEntityManager()->persist(new LogCancellations($this->getAuthentication()->getPersonObject(), $idsCancelled));

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The bookings were successfully cancelled'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    private function _search(AcademicYear $academicYear, $semester)
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $academicYear, $semester);
            case 'author':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByAuthorAndAcademicYearQuery($this->getParam('string'), $academicYear, $semester);
            case 'publisher':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByPublisherAndAcademicYearQuery($this->getParam('string'), $academicYear, $semester);
            case 'barcode':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByBarcodeAndAcademicYearQuery($this->getParam('string'), $academicYear, $semester);
        }
    }

    /**
     * @return SaleArticle|null
     */
    private function _getSaleArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the article!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->error(
                'Error',
                'No article with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return \CudiBundle\Entity\Article|null
     */
    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the article!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->error(
                'Error',
                'No article with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return int
     */
    private function _getSemester()
    {
        $semester = $this->getParam('semester');

        if ($semester == 1 || $semester == 2 || $semester == 3) {
            return $semester;
        }

        return 0;
    }
}
