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

namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Stock\Delivery,
    CudiBundle\Entity\Stock\Order\Virtual as VirtualOrder,
    CudiBundle\Form\Admin\Stock\Deliveries\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * DeliveryController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DeliveryController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Supplier')
                ->findAllQuery(),
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers' => $suppliers,
            )
        );
    }

    public function supplierAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
                ->findAllBySupplierAndPeriodQuery($supplier, $period),
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'supplier' => $supplier,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'suppliers' => $suppliers,
            )
        );
    }

    public function addAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $prefix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.article_barcode_prefix') . $this->getAcademicYear()->getCode(true);

        $form = new AddForm($this->getEntityManager(), $prefix);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article_id']);

                if ($formData['add_with_virtual_order']) {
                    $virtual = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Order\Virtual')
                        ->findNbByPeriodAndArticle($period, $article);

                    $nb = $formData['number'] - ($period->getNbOrdered($article) - $period->getNbDelivered($article) + $virtual);
                    $order = new VirtualOrder($article, $nb);
                    $this->getEntityManager()->persist($order);
                }

                $item = new Delivery($article, $formData['number'], $this->getAuthentication()->getPersonObject());
                $this->getEntityManager()->persist($item);
                $this->getEntityManager()->flush();

                $enableAssignment = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.enable_automatic_assignment');

                if ($enableAssignment == '1') {
                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Booking')
                        ->assignAllByArticle($article, $this->getMailTransport());
                    $this->getEntityManager()->flush();
                }

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The delivery was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_stock_delivery',
                    array(
                        'action' => 'add',
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        $deliveries = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Delivery')
            ->findAllByPeriodQuery($period)
            ->setMaxResults(25)
            ->getResult();

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'form' => $form,
                'deliveries' => $deliveries,
                'suppliers' => $suppliers,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (!($delivery = $this->_getDelivery()))
            return new ViewModel();

        $ordered = $period->getNbOrdered($delivery->getArticle()) + $period->getNbVirtualOrdered($delivery->getArticle());
        $delivered = $period->getNbDelivered($delivery->getArticle()) - $delivery->getNumber();

        if ($ordered > $delivered) {
            $virtualOrders = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Virtual')
                ->findAllByPeriodAndArticle($period, $delivery->getArticle());

            $diff = $ordered - $delivered;
            foreach($virtualOrders as $virtual) {
                if ($diff <= 0)
                    break;

                if ($virtual->getNumber() > $diff) {
                    $virtual->setNumber($virtual->getNumber() - $diff);
                    break;
                } else {
                    $this->getEntityManager()->remove($virtual);
                    $diff -= $virtual->getNumber();
                }
            }
        }

        $delivery->getArticle()->addStockValue(-$delivery->getNumber());
        $this->getEntityManager()->remove($delivery);
        $this->getEntityManager()->flush();

        $nbToMuchAssigned = $period->getNbAssigned($delivery->getArticle()) - $delivery->getArticle()->getStockValue();
        if ($nbToMuchAssigned > 0) {
            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findLastAssignedByArticle($delivery->getArticle());

            foreach($bookings as $booking) {
                if ($nbToMuchAssigned <= 0)
                    break;
                $booking->setStatus('booked', $this->getEntityManager());
                $nbToMuchAssigned -= $booking->getNumber();
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

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
        foreach($articles as $article) {
            $virtual = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Virtual')
                ->findNbByPeriodAndArticle($period, $article);

            $item = (object) array();
            $item->id = $article->getId();
            $item->value = $article->getMainArticle()->getTitle() . ' - ' . $article->getBarcode();
            $item->maximum = $period->getNbOrdered($article) - $period->getNbDelivered($article) + $virtual;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _getDelivery()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the delivery!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_delivery',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $delivery = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Delivery')
            ->findOneById($this->getParam('id'));

        if (null === $delivery) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No delivery with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_delivery',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $delivery;
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
                'cudi_admin_stock_delivery',
                array(
                    'action' => 'manage'
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
                'cudi_admin_stock_delivery',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $supplier;
    }
}
