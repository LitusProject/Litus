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

namespace CudiBundle\Controller\Admin\Stock;

use CudiBundle\Entity\Stock\Delivery;
use CudiBundle\Entity\Stock\Order\Virtual as VirtualOrder;
use CudiBundle\Entity\Supplier;
use Zend\View\Model\ViewModel;

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
        if (!($supplier = $this->getSupplierEntity())) {
            return new ViewModel();
        }

        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

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
        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $prefix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.article_barcode_prefix') . $this->getAcademicYearEntity()->getCode(true);

        $form = $this->getForm('cudi_stock_delivery_add', array(
            'barcode_prefix' => $prefix,
        ));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article']['id']);

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
                        ->getConfigValue('cudi.enable_automatic_assignment') &&
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.enable_assign_after_stock_update');

                if ($enableAssignment) {
                    $lilo = null;
                    if ($this->getServiceLocator()->has('lilo')) {
                        $lilo = $this->getServiceLocator()->get('lilo');
                    }

                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Booking')
                        ->assignAllByArticle($article, $this->getMailTransport(), $lilo);
                    $this->getEntityManager()->flush();
                }

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The delivery was successfully added!'
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

        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($delivery = $this->getDeliveryEntity())) {
            return new ViewModel();
        }

        $ordered = $period->getNbOrdered($delivery->getArticle()) + $period->getNbVirtualOrdered($delivery->getArticle());
        $delivered = $period->getNbDelivered($delivery->getArticle()) - $delivery->getNumber();

        if ($ordered > $delivered) {
            $virtualOrders = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Virtual')
                ->findAllByPeriodAndArticle($period, $delivery->getArticle());

            $diff = $ordered - $delivered;
            foreach ($virtualOrders as $virtual) {
                if ($diff <= 0) {
                    break;
                }

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

            foreach ($bookings as $booking) {
                if ($nbToMuchAssigned <= 0) {
                    break;
                }
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

        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

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

    /**
     * @return Delivery|null
     */
    private function getDeliveryEntity()
    {
        $delivery = $this->getEntityById('CudiBundle\Entity\Stock\Delivery');

        if (!($delivery instanceof Delivery)) {
            $this->flashMessenger()->error(
                'Error',
                'No delivery was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_delivery',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $delivery;
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
                'cudi_admin_stock_delivery',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $supplier;
    }
}
