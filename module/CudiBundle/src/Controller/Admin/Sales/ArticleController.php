<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Sales;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Form\Admin\Sales\Article\Activate as ActivateForm,
    CudiBundle\Form\Admin\Sales\Article\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Article\Edit as EditForm,
    CudiBundle\Entity\Sales\Article as SaleArticle,
    CudiBundle\Entity\Sales\History,
    CudiBundle\Entity\Sales\SaleItem,
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

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Article')
                ->findAllByAcademicYear($academicYear),
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        if ($article->isInternal()) {
            $priceBW = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.price_page_black_and_white') / 100;
            $priceColor = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.price_page_color') / 100;
            $pricePerforated = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.price_perforated') / 100;
            $precalculatedPrice = $article->getNbBlackAndWhite() * $priceBW
                + $article->getNbColored() * $priceColor
                + ($article->isPerforated() ? $pricePerforated : 0 );
        } else {
            $precalculatedPrice = 0;
        }

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $supplier = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Supplier')
                    ->findOneById($formData['supplier']);

                $saleArticle = new SaleArticle(
                    $article,
                    $formData['barcode'],
                    $formData['purchase_price'],
                    $formData['sell_price'],
                    $formData['bookable'],
                    $formData['unbookable'],
                    $supplier,
                    $formData['can_expire'],
                    $this->getCurrentAcademicYear()
                );

                $this->getEntityManager()->persist($saleArticle);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The sale article was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_sales_article',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
                'precalculatedPrice' => $precalculatedPrice,
            )
        );
    }

    public function editAction()
    {
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $this->getCurrentAcademicYear(), $saleArticle);

        if ($saleArticle->getMainArticle()->isInternal()) {
            $priceBW = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.price_page_black_and_white') / 100;
            $priceColor = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.price_page_color') / 100;
            $pricePerforated = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.price_perforated') / 100;
            $precalculatedPrice = $saleArticle->getMainArticle()->getNbBlackAndWhite() * $priceBW
                + $saleArticle->getMainArticle()->getNbColored() * $priceColor
                + ($saleArticle->getMainArticle()->isPerforated() ? $pricePerforated : 0);
        } else {
            $precalculatedPrice = 0;
        }

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $history = new History($saleArticle);
                $this->getEntityManager()->persist($history);

                $supplier = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Supplier')
                    ->findOneById($formData['supplier']);

                $saleArticle->setBarcode($formData['barcode'])
                    ->setPurchasePrice($formData['purchase_price'])
                    ->setSellPrice($formData['sell_price'])
                    ->setIsBookable($formData['bookable'])
                    ->setIsUnbookable($formData['unbookable'])
                    ->setSupplier($supplier)
                    ->setCanExpire($formData['can_expire']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The sale article was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_sales_article',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $saleArticle,
                'precalculatedPrice' => $precalculatedPrice,
            )
        );
    }

    public function activateAction()
    {
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $form = new ActivateForm($this->getEntityManager(), $this->getCurrentAcademicYear(), $saleArticle);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $new = $saleArticle->duplicate();

                $supplier = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Supplier')
                    ->findOneById($formData['supplier']);

                $new->setBarcode($formData['barcode'])
                    ->setPurchasePrice($formData['purchase_price'])
                    ->setSellPrice($formData['sell_price'])
                    ->setIsBookable($formData['bookable'])
                    ->setIsUnbookable($formData['unbookable'])
                    ->setSupplier($supplier)
                    ->setCanExpire($formData['can_expire'])
                    ->setAcademicYear($this->getCurrentAcademicYear());

                $this->getEntityManager()->persist($new);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The sale article was successfully activated for this academic year!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_sales_article',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $saleArticle->getMainArticle(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $saleArticle->setIsHistory(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        switch($this->getParam('field')) {
            case 'title':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByTitleAndAcademicYear($this->getParam('string'), $this->getAcademicYear());
                break;
            case 'author':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByAuthorAndAcademicYear($this->getParam('string'), $this->getAcademicYear());
                break;
            case 'publisher':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByPublisherAndAcademicYear($this->getParam('string'), $this->getAcademicYear());
                break;
            case 'barcode':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByBarcodeAndAcademicYear($this->getParam('string'), $this->getAcademicYear());
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach($articles as $article) {
            $item = (object) array();
            $item->id = $article->getMainArticle()->getId();
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

    public function sellProfAction()
    {
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $saleItem = new SaleItem(
            $saleArticle,
            1,
            0,
            null,
            $this->getEntityManager()
        );
        $this->getEntityManager()->persist($saleItem);

        $saleArticle->setStockValue($saleArticle->getStockValue() - 1);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The article is successfully sold to a prof'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findAllByTitleAndAcademicYearTypeAhead($this->getParam('string'), $academicYear);

        $result = array();
        foreach($articles as $article) {
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

    private function _getSaleArticle()
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
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
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
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
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
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
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
                'admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }
}
