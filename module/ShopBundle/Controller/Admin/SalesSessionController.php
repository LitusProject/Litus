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

namespace ShopBundle\Controller\Admin;

use ShopBundle\Entity\Session as SalesSession;
use ShopBundle\Entity\Session\Stock;
use Zend\View\Model\ViewModel;

/**
 * SalesSessionController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class SalesSessionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Session')
                ->findAllFutureQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Session')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $products = $this->getAvailableProducts();

        $form = $this->getForm(
            'shop_salesSession_add',
            array(
                'products' => $products,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $salesSession = $form->hydrateObject();
                $this->getEntityManager()->persist($salesSession);

                foreach ($products as $product) {
                    $amount = $formData[$product->getId() . '-quantity'];
                    if ($amount == 0) {
                        continue;
                    }
                    $stock = new Stock();
                    $stock->setSalesSession($salesSession);
                    $stock->setProduct($product);
                    $stock->setAmount($amount);
                    $this->getEntityManager()->persist($stock);
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The sales session was successfully created!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_salessession',
                    array(
                        'action' => 'add',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $salesSession = $this->getSalesSessionEntity();
        if ($salesSession === null) {
            return new ViewModel();
        }

        $products = $this->getAvailableAndStockAndReservationProducts($salesSession);
        $form = $this->getForm(
            'shop_salesSession_edit',
            array(
                'salesSession' => $salesSession,
                'products'     => $products,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $repository = $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Session\Stock');
                $repository->deleteStockEntries($salesSession);
                foreach ($products as $product) {
                    $amount = $formData[$product->getId() . '-quantity'];
                    if ($amount == 0) {
                        continue;
                    }
                    $stock = new Stock();
                    $stock->setSalesSession($salesSession);
                    $stock->setProduct($product);
                    $stock->setAmount($amount);
                    $this->getEntityManager()->persist($stock);
                }

                $reservations = $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Reservation')
                    ->findBySalesSession($salesSession);

                foreach ($reservations as $reservation) {
                    if ($repository->getRealAvailability($reservation->getProduct(), $salesSession) < 0) {
                        $this->flashMessenger()->warn(
                            'Warning',
                            'Some products are overbooked.'
                        );
                        break;
                    }
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The session was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_salessession',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $salesSession = $this->getSalesSessionEntity();
        if ($salesSession === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($salesSession);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $salesSessions = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($salesSessions as $session) {
            $item = (object) array();
            $item->id = $session->getId();
            $item->start_date = $session->getStartDate()->format('d/m/Y H:i');
            $item->end_date = $session->getEndDate()->format('d/m/Y H:i');
            $item->final_reservation_date = $session->getReservationDate()->format('d/m/Y H:i');
            $item->remarks = $session->getRemarks();
            $item->reservations_possible = $session->getReservationsPossible();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    // TOOD: Rename to oldSearchAction()
    public function oldsearchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $salesSessions = $this->searchOld()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($salesSessions as $session) {
            $item = (object) array();
            $item->id = $session->getId();
            $item->start_date = $session->getStartDate()->format('d/m/Y H:i');
            $item->end_date = $session->getEndDate()->format('d/m/Y H:i');
            $item->final_reservation_date = $session->getReservationDate()->format('d/m/Y H:i');
            $item->remarks = $session->getRemarks();
            $item->reservations_possible = $session->getReservationsPossible();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'remarks':
                return $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Session')
                    ->findAllFutureByRemarksQuery($this->getParam('string'));
        }
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function searchOld()
    {
        switch ($this->getParam('field')) {
            case 'remarks':
                return $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Session')
                    ->findAllOldByRemarksQuery($this->getParam('string'));
        }
    }

    /**
     * @return array
     */
    protected function getAvailableProducts()
    {
        return $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Product')
            ->findAllAvailable();
    }

    /**
     * @return array
     */
    protected function getAvailableAndStockAndReservationProducts($salesSession)
    {
        return $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Product')
            ->findAvailableAndStockAndReservation($salesSession);
    }

    /**
     * @return SalesSession|null
     */
    private function getSalesSessionEntity()
    {
        $salesSession = $this->getEntityById('ShopBundle\Entity\Session');

        if (!($salesSession instanceof SalesSession)) {
            $this->flashMessenger()->error(
                'Error',
                'No session was found!'
            );
            $this->redirect()->toRoute(
                'shop_admin_shop_salessession',
                array(
                    'action' => 'manage',
                )
            );

            return null;
        }

        return $salesSession;
    }
}
