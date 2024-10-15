<?php

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use CommonBundle\Entity\General\AcademicYear;
use CudiBundle\Component\Document\Generator\SaleArticles as SaleArticlesGenerator;
use CudiBundle\Entity\Article;
use CudiBundle\Entity\Article\Internal as InternalArticle;
use CudiBundle\Entity\Log\Article\Sale\Bookable as BookableLog;
use CudiBundle\Entity\Log\Article\Sale\Unbookable as UnbookableLog;
use CudiBundle\Entity\Log\Sale\Cancellations as LogCancellations;
use CudiBundle\Entity\Sale\Article as SaleArticle;
use CudiBundle\Entity\Sale\Article\History;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        $semester = $this->getSemester();

        if ($this->getParam('field') !== null) {
            $articles = $this->search($academicYear, $semester);
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
                'currentSemester'     => $semester,
                'academicYears'       => $academicYears,
                'activeAcademicYear'  => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(true),
            )
        );
    }

    public function exportAction()
    {
        $form = $this->getForm('cudi_sale_article_export');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'cudi_admin_sales_article',
                array('action' => 'download')
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
                $common = $formData['common'];

                $file = new CsvFile();
                $document = new SaleArticlesGenerator($this->getEntityManager(), $academicYear, $formData['semester'], $common);
                $document->generateDocument($file);

                $this->getResponse()->getHeaders()
                    ->addHeaders(
                        array(
                            'Content-Disposition' => 'attachment; filename="sale_articles_' . $semester . '_' . $academicYear->getCode() . '.csv"',
                            'Content-Type'        => 'text/csv',
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
        $article = $this->getArticleEntity();
        if ($article === null) {
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
                        'id'     => $saleArticle->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'                       => $form,
                'article'                    => $article,
                'precalculatedSellPrice'     => $precalculatedSellPrice,
                'precalculatedPurchasePrice' => $precalculatedPurchasePrice,
            )
        );
    }

    public function editAction()
    {
        $saleArticle = $this->getSaleArticleEntity();
        if ($saleArticle === null) {
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

                    if ($mainArticle->getFrontPage() !== null && file_exists($cachePath . '/' . $mainArticle->getFrontPage())) {
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
                        'id'     => $saleArticle->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'                       => $form,
                'article'                    => $saleArticle,
                'precalculatedSellPrice'     => $precalculatedSellPrice,
                'precalculatedPurchasePrice' => $precalculatedPurchasePrice,
            )
        );
    }

    public function viewAction()
    {
        $saleArticle = $this->getSaleArticleEntity();
        if ($saleArticle === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_view', array('article' => $saleArticle));

        return new ViewModel(
            array(
                'form'    => $form,
                'article' => $saleArticle,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $saleArticle = $this->getSaleArticleEntity();
        if ($saleArticle === null) {
            return new ViewModel();
        }
        
        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllActiveByArticleAndPeriod($saleArticle, $this->getActiveStockPeriodEntity());

        $idsCancelled = array();
        foreach ($bookings as $booking) {
            $booking->setStatus('canceled', $this->getEntityManager());
            $idsCancelled[] = $booking->getId();
        }

        $saleArticle = $this->getSaleArticleEntity();
        if ($saleArticle === null) {
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
        $saleArticle = $this->getSaleArticleEntity();
        if ($saleArticle === null) {
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

        $this->redirect()->toRoute(
            'cudi_admin_sales_article',
            array(
                'action' => 'edit',
                'id'     => $saleArticle->getId(),
            )
        );

        return new ViewModel();
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $semester = $this->getSemester();
        $articles = $this->search($this->getAcademicYearEntity(), $semester)
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
            $item->sellPrice = number_format($article->getSellPrice() / 100, 2);
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
        $article = $this->getSaleArticleEntity();
        if ($article === null) {
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

        $academicYear = $this->getAcademicYearEntity();

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

            if ($article->getBarcode() > 0) {
                $item->value = $article->getMainArticle()->getTitle() . ' - ' . $article->getBarcode();
            } else {
                $item->value = $article->getMainArticle()->getTitle();
            }
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
        $saleArticle = $this->getSaleArticleEntity();
        if ($saleArticle === null) {
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
                        ->findAllByStatusAndArticleAndPeriod($status, $saleArticle, $this->getActiveStockPeriodEntity());

                    foreach ($bookings as $booking) {
                        if (isset($persons[$booking->getPerson()->getId()])) {
                            continue;
                        }

                        $persons[$booking->getPerson()->getId()] = true;

                        $mail = new Message();
                        $mail->setEncoding('UTF-8')
                            ->setBody($formData['message'])
                            ->setFrom($mailAddress, $mailName)
                            ->addTo($booking->getPerson()->getEmail(), $booking->getPerson()->getFullName())
                            ->setSubject($formData['subject']);

                        if (getenv('APPLICATION_ENV') != 'development') {
                            $this->getMailTransport()->send($mail);
                        }
                    }
                }

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The email was successfully sent to ' . count($persons) . ' academics!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
                    array(
                        'action' => 'edit',
                        'id'     => $saleArticle->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'article' => $saleArticle,
                'form'    => $form,
            )
        );
    }

    public function cancelBookingsAction()
    {
        $saleArticle = $this->getSaleArticleEntity();
        if ($saleArticle === null) {
            return new ViewModel();
        }

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllActiveByArticleAndPeriod($saleArticle, $this->getActiveStockPeriodEntity());

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

        $this->redirect()->toRoute(
            'cudi_admin_sales_article',
            array(
                'action' => 'edit',
                'id'     => $saleArticle->getId(),
            )
        );

        return new ViewModel();
    }

    /**
     * @param  AcademicYear $academicYear
     * @param  integer      $semester
     * @return \Doctrine\ORM\Query|null
     */
    private function search(AcademicYear $academicYear, $semester)
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
    private function getSaleArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Sale\Article');

        if (!($article instanceof SaleArticle)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
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
     * @return Article|null
     */
    private function getArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Article');

        if (!($article instanceof Article)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
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
     * @return integer
     */
    private function getSemester()
    {
        $semester = (int) $this->getParam('semester');

        if ($semester == 1 || $semester == 2 || $semester == 3) {
            return $semester;
        }

        return 0;
    }
}
