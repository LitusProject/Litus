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

use ShopBundle\Entity\Reservation;
use ShopBundle\Entity\Reservation\Permission as ReservationPermission;
use ShopBundle\Entity\Session as SalesSession;
use Zend\View\Model\ViewModel;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Zend\Http\Headers;

/**
 * ReservationController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ReservationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function salessessionAction()
    {
        $salesSession = $this->getSalesSessionEntity();
        if ($salesSession === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Reservation')
                ->findBySalesSessionQuery($salesSession),
            $this->getParam('page')
        );

        $result = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation')
            ->getTotalByProductBySalesQuery($salesSession)
            ->getResult();

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'totals'            => $result,
                'salesSession'      => $salesSession,
            )
        );
    }

    public function deleteAction()
    {
        $reservation = $this->getReservationEntity();
        if ($reservation === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($reservation);
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

        $reservation = $this->getReservationEntity();
        if ($reservation === null) {
            return new ViewModel();
        }

        $reservation->setNoShow(!$reservation->getNoShow());
        $blacklisted = false;
        $blacklistAvoided = false;

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
                    ->getRepository('ShopBundle\Entity\Reservation\Permission')
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

    public function csvAction()
    {
        $salesSession = $this->getSalesSessionEntity();
        if ($salesSession === null) {
            return new ViewModel();
        }
        $items = $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Reservation')
                    ->findBySalesSessionQuery($salesSession)
                    ->getResult();
        $file = new CsvFile();

        $heading = array('Person', 'Product', 'Amount','Total Price', 'Picked Up');
        $results = array();
        foreach ($items as $item) {
            $results[] = array(
                $item->getPerson()->getFullName(),
                $item->getProduct()->getName(),
                (string) $item->getAmount(),
                (string) $item->getAmount() * $item->getProduct()->getSellPrice(),
                ' ',
          );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $filename = 'salesession ' . $salesSession->getStartDate()->format('Ymd') . '.csv';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename=' . $filename,
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        // $numResults = $this->getEntityManager()
        //     ->getRepository('CommonBundle\Entity\General\Config')
        //     ->getConfigValue('search_max_results');
        $reservations = $this->search()
            ->getResult();


        $result = array();
        foreach ($reservations as $reservation) {
            $item = (object) array();
            $item->id = $reservation->getId();
            $item->person = $reservation->getPerson()->getFullName();
            $item->product = $reservation->getProduct()->getName();
            $item->amount = $reservation->getAmount();
            $item->total = $reservation->getAmount() * $reservation->getProduct()->getSellPrice();
            $item->noShow = $reservation->getNoShow();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
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

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Reservation')
                    ->getAllReservationsByPersonAndSalesSessionQuery($this->getParam('string'), $this->getSalesSessionEntity());
        }
    }
}
