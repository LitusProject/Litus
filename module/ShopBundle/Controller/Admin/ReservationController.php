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

use ShopBundle\Entity\Reservation,
    ShopBundle\Entity\ReservationPermission,
    ShopBundle\Entity\SalesSession,
    Zend\View\Model\ViewModel;

/**
 * ReservationController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ReservationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function salessessionAction()
    {
        if (!($salesSession = $this->getSalesSessionEntity())) {
            return new ViewModel();
        }
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Reservation')
                ->findBySalesSessionQuery($salesSession),
            $this->getParam('page'));

        $result = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation')
            ->getTotalByProductBySalesQuery($salesSession);
//        $paginator_total = $this->paginator()->createFromArray($result, $this->getParam('page_total'));

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'totals'   => $result,
                'salesSession'      => $salesSession,
            )
        );
    }

    public function deleteAction()
    {
        if (!($reseration = $this->getReservationEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($reseration);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function noshowAction()
    {
        $this->initAjax();
        if (!($reservation = $this->getReservationEntity())) {
            return new ViewModel();
        }

        $reservation->setNoShow(!$reservation->getNoShow());
        $blacklisted = false;
        $blacklistAvoided = false; // person should be blacklisted but has special reservation permission

        $this->getEntityManager()->persist($reservation);
        $this->getEntityManager()->flush();

        if ($reservation->getNoShow()) {
            $maxNoShows = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shop.maximal_no_shows');
            $currentNoShows = $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Reservation')
                ->getNoShowSessionCount($reservation->getPerson());
            if ($currentNoShows >= $maxNoShows) {
                $reservationPermission = $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\ReservationPermission')
                    ->find($reservation->getPerson());
                if ($reservationPermission) {
                    $blacklistAvoided = $reservationPermission->getReservationsAllowed();
                } else {
                    $blacklisted = true;
                    $reservationPermission = new ReservationPermission();
                    $reservationPermission->setPerson($reservation->getPerson());
                    $reservationPermission->setReservationsAllowed(false);
                    $this->getEntityManager()->persist($reservationPermission);
                    $this->getEntityManager()->flush();
                }
            }
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status'           => 'success',
                    'blacklisted'      => $blacklisted,
                    'blacklistAvoided' => $blacklistAvoided,
                ),
            )
        );
    }

    /**
     * @return Reservation|null
     */
    private function getReservationEntity()
    {
        $reservation = $this->getEntityById('ShopBundle\Entity\Reservation');
        if (!($reservation instanceof Reservation)) {
            $this->flashMessenger()->error(
                'Error',
                'No reservation was found!'
            );
            $this->redirect()->toRoute(
                'shop_admin_shop_reservation',
                array(
                    'action' => 'manage',
                )
            );

            return null;
        }

        return $reservation;
    }

    /**
     * @return SalesSession|null
     */
    private function getSalesSessionEntity()
    {
        $salesSession = $this->getEntityById('ShopBundle\Entity\SalesSession');
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
