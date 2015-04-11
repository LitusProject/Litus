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

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Component\Document\Generator\Stock as StockGenerator,
    CudiBundle\Entity\Stock\Delivery,
    CudiBundle\Entity\Stock\Order\Virtual as VirtualOrder,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Stock\Period\Value\Delta,
    Cudibundle\Entity\Sale\Article as SaleArticle,
    Zend\Http\Headers,
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
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        $semester = $this->getSemester();

        if (null !== $this->getParam('field')) {
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

        return new ViewModel(
            array(
                'currentSemester' => $semester,
                'period' => $period,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function notDeliveredAction()
    {
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $articles = $this->searchNotDelivered($period);
        }

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($period, true);
        }

        $paginator = $this->paginator()->createFromArray(
            $articles,
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
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        $semester = $this->getSemester();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->search($this->getAcademicYearEntity(), $semester)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->supplier = $article->getSupplier()->getName();
            $item->nbAssigned = $period->getNbAssigned($article);
            $item->nbNotAssigned = $period->getNbBooked($article);
            $item->nbInStock = $article->getStockValue();

            $ordered = $period->getNbOrdered($article);
            $virtualOrdered = $period->getNbVirtualOrdered($article);
            $delivered = $period->getNbDelivered($article);

            $item->nbNotDelivered = max(0, $ordered - $delivered);
            $item->nbOrdered = $ordered;
            $item->nbVirtualOrdered = $virtualOrdered;
            $item->nbNotDeliveredVirtual = max(0, $ordered + $virtualOrdered - $delivered);

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
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        $articles = $this->searchNotDelivered($period);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach ($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->supplier = $article->getSupplier()->getName();
            $item->nbAssigned = $period->getNbAssigned($article);
            $item->nbNotAssigned = $period->getNbBooked($article);
            $item->nbInStock = $article->getStockValue();

            $ordered = $period->getNbOrdered($article);
            $virtualOrdered = $period->getNbVirtualOrdered($article);
            $delivered = $period->getNbDelivered($article);

            $item->nbNotDelivered = max(0, $ordered - $delivered);
            $item->nbOrdered = $ordered;
            $item->nbVirtualOrdered = $virtualOrdered;
            $item->nbNotDeliveredVirtual = max(0, $ordered + $virtualOrdered - $delivered);

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
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($article = $this->getSaleArticleEntity())) {
            return new ViewModel();
        }

        $deliveryForm = $this->getForm('cudi_stock_delivery_add-direct');
        $orderForm = $this->getForm('cudi_stock_order_add-direct');
        $stockForm = $this->getForm('cudi_stock_update', array('article' => $article));

        $virtual = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Order\Virtual')
            ->findNbByPeriodAndArticle($period, $article);
        $maxDelivery = $period->getNbOrdered($article) - $period->getNbDelivered($article) + $virtual;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['updateStock'])) {
                $stockForm->setData($formData);
                if ($stockForm->isValid()) {
                    $formData = $stockForm->getData();

                    if ($formData['number'] != $article->getStockValue()) {
                        $delta = new Delta(
                            $this->getAuthentication()->getPersonObject(),
                            $article,
                            $period,
                            $formData['number'] - $article->getStockValue(),
                            $formData['comment']
                        );
                        $this->getEntityManager()->persist($delta);

                        $article->setStockValue($formData['number']);

                        $nbToMuchAssigned = $period->getNbAssigned($article) - $article->getStockValue();
                        $bookings = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Booking')
                            ->findLastAssignedByArticle($article);

                        foreach ($bookings as $booking) {
                            if ($nbToMuchAssigned <= 0) {
                                break;
                            }
                            $booking->setStatus('booked', $this->getEntityManager());
                            $nbToMuchAssigned -= $booking->getNumber();
                        }

                        $enableAssignment = $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('cudi.enable_automatic_assignment') &&
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('cudi.enable_assign_after_stock_update');

                        if ($enableAssignment) {
                            $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Booking')
                                ->assignAllByArticle($article, $this->getMailTransport());
                            $this->getEntityManager()->flush();
                        }

                        $this->getEntityManager()->flush();
                    }

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The stock was successfully updated!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_admin_stock',
                        array(
                            'action' => 'edit',
                            'id' => $article->getId(),
                        )
                    );

                    return new ViewModel();
                }
            } elseif (isset($formData['add_order'])) {
                $orderForm->setData($formData);
                if ($orderForm->isValid()) {
                    $formData = $orderForm->getData();

                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Order\Order')
                        ->addNumberByArticle($article, $formData['number'], $this->getAuthentication()->getPersonObject());

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The order was successfully added!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_admin_stock',
                        array(
                            'action' => 'edit',
                            'id' => $article->getId(),
                        )
                    );

                    return new ViewModel();
                }
            } else {
                $deliveryForm->setData($formData);

                if ($deliveryForm->isValid()) {
                    $formData = $deliveryForm->getData();

                    if ($formData['add_with_virtual_order']) {
                        $nb = $formData['number'] - ($period->getNbOrdered($article) - $period->getNbDelivered($article) + $virtual);
                        $order = new VirtualOrder($article, $nb);
                        $this->getEntityManager()->persist($order);
                    }

                    $delivery = new Delivery($article, $formData['number'], $this->getAuthentication()->getPersonObject());
                    $this->getEntityManager()->persist($delivery);
                    $this->getEntityManager()->flush();

                    $enableAssignment = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.enable_automatic_assignment') &&
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.enable_assign_after_stock_update');

                    if ($enableAssignment) {
                        $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Booking')
                            ->assignAllByArticle($article, $this->getMailTransport());
                        $this->getEntityManager()->flush();
                    }

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The delivery was successfully added!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_admin_stock',
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
                'maxDelivery' => $maxDelivery,
            )
        );
    }

    public function viewAction()
    {
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($article = $this->getSaleArticleEntity())) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'article' => $article,
                'period' => $period,
            )
        );
    }

    public function deltaAction()
    {
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($article = $this->getSaleArticleEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period\Value\Delta')
                ->findAllByArticleAndPeriodQuery($article, $period),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'article' => $article,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function exportAction()
    {
        $form = $this->getForm('cudi_stock_export');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'cudi_admin_stock',
                array(
                    'action' => 'download',
                )
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
        $form = $this->getForm('cudi_stock_export');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $file = new TmpFile();
                $document = new StockGenerator(
                    $this->getEntityManager(),
                    $formData['articles'],
                    $formData['order'],
                    isset($formData['in_stock']) && $formData['in_stock'],
                    $this->getAcademicYearEntity(),
                    $file
                );
                $document->generate();

                $headers = new Headers();
                $headers->addHeaders(array(
                    'Content-Disposition' => 'attachment; filename="stock.pdf"',
                    'Content-Type'        => 'application/pdf',
                ));
                $this->getResponse()->setHeaders($headers);

                return new ViewModel(
                    array(
                        'data' => $file->getContent(),
                    )
                );
            }
        }
    }

    public function bulkUpdateAction()
    {
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if ($this->getRequest()->getQuery('select') == null) {
            $form = $this->getForm('cudi_stock_select-options');
            $form->setAttribute('method', 'get');

            return new ViewModel(
                array(
                    'form' => $form,
                )
            );
        } else {
            $formData = $this->getRequest()->getQuery();
            if ($formData['order'] == 'barcode') {
                $stock = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByAcademicYearSortBarcode($this->getAcademicYearEntity());
            } else {
                $stock = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByAcademicYear($this->getAcademicYearEntity());
            }

            $articles = array();
            foreach ($stock as $item) {
                if ($formData['articles'] == 'external' && $item->getMainArticle()->isInternal()) {
                    continue;
                }
                if ($formData['articles'] == 'internal' && !$item->getMainArticle()->isInternal()) {
                    continue;
                }

                if ($item->getStockValue() <= 0 && isset($formData['in_stock']) && $formData['in_stock']) {
                    continue;
                }

                $articles[] = $item;
            }

            $form = $this->getForm('cudi_stock_bulk-update', array(
                'articles' => $articles,
            ));

            if ($this->getRequest()->isPost()) {
                $form->setData($this->getRequest()->getPost());

                if ($form->isValid()) {
                    $formData = $form->getData();

                    foreach ($articles as $article) {
                        if ($article->getStockValue() != $formData['article_' . $article->getId()]) {
                            $delta = new Delta(
                                $this->getAuthentication()->getPersonObject(),
                                $article,
                                $period,
                                $formData['article_' . $article->getId()] - $article->getStockValue(),
                                'Stock Update'
                            );
                            $this->getEntityManager()->persist($delta);

                            $article->setStockValue($formData['article_' . $article->getId()]);

                            $nbToMuchAssigned = $period->getNbAssigned($article) - $article->getStockValue();
                            $bookings = $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Booking')
                                ->findLastAssignedByArticle($article);

                            foreach ($bookings as $booking) {
                                if ($nbToMuchAssigned <= 0) {
                                    break;
                                }
                                $booking->setStatus('booked', $this->getEntityManager());
                                $nbToMuchAssigned -= $booking->getNumber();
                            }
                        }
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The stock was successfully updated!'
                    );

                    $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));
                }
            }

            return new ViewModel(
                array(
                    'articles' => $articles,
                    'form' => $form,
                    'period' => $period,
                )
            );
        }
    }

    private function search(AcademicYear $academicYear, $semester = 0)
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $academicYear, $semester);
            case 'barcode':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllByBarcodeAndAcademicYearQuery($this->getParam('string'), $academicYear, $semester);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findAllBySupplierStringAndAcademicYearQuery($this->getParam('string'), $academicYear, $semester);
        }
    }

    private function searchNotDelivered(Period $period)
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndTitle($period, $this->getParam('string'), true);
            case 'barcode':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndBarcode($period, $this->getParam('string'), true);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndSupplier($period, $this->getParam('string'), true);
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
                'cudi_admin_stock',
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
    private function getSemester()
    {
        $semester = $this->getParam('semester');

        if ($semester == 1 || $semester == 2  || $semester == 3) {
            return $semester;
        }

        return 0;
    }
}
