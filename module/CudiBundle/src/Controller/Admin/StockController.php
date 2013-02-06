<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    CudiBundle\Component\Document\Generator\Stock as StockGenerator,
    CudiBundle\Form\Admin\Stock\Export as ExportForm,
    CudiBundle\Form\Admin\Stock\Deliveries\AddDirect as DeliveryForm,
    CudiBundle\Form\Admin\Stock\Orders\AddDirect as OrderForm,
    CudiBundle\Form\Admin\Stock\Update as StockForm,
    CudiBundle\Entity\Stock\Delivery,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Stock\Periods\Values\Delta,
    CudiBundle\Entity\Stock\Orders\Virtual as VirtualOrder,
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
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $academicYear = $this->getAcademicYear();
        $semester = $this->_getSemester();
        $organization = $this->_getOrganization();

        if (null !== $this->getParam('field'))
            $articles = $this->_search($academicYear, $semester, $organization);

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Article')
                ->findAllByAcademicYear($academicYear, $semester, $organization);
        }

        $paginator = $this->paginator()->createFromArray(
            $articles,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'currentSemester' => $semester,
                'period' => $period,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'organizations' => $organizations,
                'currentOrganization' => $organization,
            )
        );
    }

    public function notDeliveredAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (null !== $this->getParam('field'))
            $articles = $this->_searchNotDelivered($period);

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
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $semester = $this->_getSemester();
        $organization = $this->_getOrganization();

        $articles = $this->_search($this->getAcademicYear(), $semester, $organization);

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
            $item->nbAssigned = $period->getNbAssigned($article);
            $item->nbNotAssigned = $period->getNbBooked($article);
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

        $articles = $this->_searchNotDelivered($period);

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

        $virtual = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Orders\Virtual')
            ->findNbByPeriodAndArticle($period, $article);
        $maxDelivery = $period->getNbOrdered($article) - $period->getNbDelivered($article) + $virtual;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['updateStock'])) {
                $stockForm->setData($formData);
                if ($stockForm->isValid()) {
                    $formData = $stockForm->getFormData($formData);

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
                        ->getRepository('CudiBundle\Entity\Sales\Booking')
                        ->findLastAssignedByArticle($article);

                    foreach($bookings as $booking) {
                        if ($nbToMuchAssigned <= 0)
                            break;
                        $booking->setStatus('booked', $this->getEntityManager());
                        $nbToMuchAssigned -= $booking->getNumber();
                    }

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
                $orderForm->setData($formData);
                if ($orderForm->isValid()) {
                    $formData = $orderForm->getFormData($formData);

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
            } else {
                $deliveryForm->setData($formData);

                if ($deliveryForm->isValid()) {
                    $formData = $deliveryForm->getFormData($formData);

                    if ($formData['add_with_virtual_order']) {
                        $nb = $formData['number'] - ($period->getNbOrdered($article) - $period->getNbDelivered($article) + $virtual);
                        $order = new VirtualOrder($article, $nb);
                        $this->getEntityManager()->persist($order);
                    }

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
                'maxDelivery' => $maxDelivery,
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

    public function exportAction()
    {
        $form = new ExportForm(
            $this->url()->fromRoute(
                'admin_stock',
                array(
                    'action' => 'download'
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
        $form = new ExportForm('');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $file = new TmpFile();
                $document = new StockGenerator($this->getEntityManager(), $formData['articles'], $formData['order'], $this->getAcademicYear(), $file);
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

    private function _search(AcademicYear $academicYear, $semester = 0, Organization $organization = null)
    {
        switch($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByTitleAndAcademicYear($this->getParam('string'), $academicYear, $semester, $organization);
            case 'barcode':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllByBarcodeAndAcademicYear($this->getParam('string'), $academicYear, $semester, $organization);
            case 'supplier':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findAllBySupplierStringAndAcademicYear($this->getParam('string'), $academicYear, $semester, $organization);
        }
    }

    private function _searchNotDelivered(Period $period)
    {
        switch($this->getParam('field')) {
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

    private function _getSemester()
    {
        $semester = $this->getParam('semester');

        if ($semester == 1 || $semester == 2)
            return $semester;
        return 0;
    }

    private function _getOrganization()
    {
        if (null !== $this->getParam('organization'))
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneById($this->getParam('organization'));

        return null;
    }
}
