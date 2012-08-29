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

namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Form\Admin\Stock\Deliveries\AddDirect as DeliveryForm,
    CudiBundle\Form\Admin\Stock\Orders\AddDirect as OrderForm,
    CudiBundle\Form\Admin\Stock\Update as StockForm,
    CudiBundle\Entity\Stock\Delivery,
    CudiBundle\Entity\Stock\Periods\Values\Delta,
    Zend\View\Model\ViewModel;

/**
 * StockController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StockController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Article')
                ->findAllByAcademicYear($this->getAcademicYear()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'period' => $period,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function notDeliveredAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($period, true),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'period' => $period,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function searchAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        switch($this->getParam('field')) {
            case 'title':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByTitleAndAcademicYear($this->getParam('string'), $this->getAcademicYear());
                break;
            case 'barcode':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByBarcodeAndAcademicYear($this->getParam('string'), $this->getAcademicYear());
                break;
            case 'supplier':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllBySupplierStringAndAcademicYear($this->getParam('string'), $this->getAcademicYear());
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->supplier = $article->getSupplier()->getName();
            $item->nbInStock = $article->getStockValue();
            $item->nbNotDelivered = $period->getNbOrdered($article) - $period->getNbDelivered($article);
            $item->nbNotDelivered = $item->nbNotDelivered < 0 ? 0 : $item->nbNotDelivered;
            $item->nbReserved = $period->getNbBooked($article) + $period->getNbAssigned($article);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchNotDeliveredAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        switch($this->getParam('field')) {
            case 'title':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndTitle($period, $this->getParam('string'), true);
                break;
            case 'barcode':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndBarcode($period, $this->getParam('string'), true);
                break;
            case 'supplier':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndSupplier($period, $this->getParam('string'), true);
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->supplier = $article->getSupplier()->getName();
            $item->nbInStock = $article->getStockValue();
            $item->nbNotDelivered = $period->getNbOrdered($article) - $period->getNbDelivered($article);
            $item->nbNotDelivered = $item->nbNotDelivered < 0 ? 0 : $item->nbNotDelivered;
            $item->nbReserved = $period->getNbBooked($article) + $period->getNbAssigned($article);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function editAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $deliveryForm = new DeliveryForm($this->getEntityManager());
        $orderForm = new OrderForm($this->getEntityManager());
        $stockForm = new StockForm($article);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if (isset($formData['updateStock'])) {
                if ($stockForm->isValid()) {
                    $delta = new Delta(
                        $this->getAuthentication()->getPersonObject(),
                        $article,
                        $period,
                        $formData['number'] - $article->getStockValue(),
                        $formData['comment']
                    );
                    $this->getEntityManager()->persist($delta);

                    $article->setStockValue($formData['number']);

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'The stock was successfully updated!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_stock',
                        array(
                            'action' => 'edit',
                            'id' => $article->getId(),
                        )
                    );

                    return new ViewModel();
                }
            } elseif (isset($formData['add_order'])) {
                if ($orderForm->isValid()) {
                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Orders\Order')
                        ->addNumberByArticle($article, $formData['number'], $this->getAuthentication()->getPersonObject());

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'The order was successfully added!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_stock',
                        array(
                            'action' => 'edit',
                            'id' => $article->getId(),
                        )
                    );

                    return new ViewModel();
                }
            } elseif (isset($formData['add_delivery'])) {
                if ($deliveryForm->isValid()) {
                    $delivery = new Delivery($article, $formData['number'], $this->getAuthentication()->getPersonObject());
                    $this->getEntityManager()->persist($delivery);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'The delivery was successfully added!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_stock',
                        array(
                            'action' => 'edit',
                            'id' => $article->getId(),
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'article' => $article,
                'period' => $period,
                'deliveryForm' => $deliveryForm,
                'orderForm' => $orderForm,
                'stockForm' => $stockForm,
            )
        );
    }

    public function deltaAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Periods\Values\Delta',
            $this->getParam('page'),
            array(
                'article' => $article,
                'period' => $period,
            ),
            array('timestamp' => 'DESC')
        );

        return new ViewModel(
            array(
                'article' => $article,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the sale article!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneById($this->getParam('id'));

        if (null === $item) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No sale article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_stock',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $item;
    }
}
