<?php

namespace ShopBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateInterval;
use DateTime;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use ShopBundle\Component\NoShow\NoShowConfig;
use ShopBundle\Entity\Reservation;
use ShopBundle\Entity\Reservation\Ban;
use ShopBundle\Entity\Session as SalesSession;

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

        $totalReservations = 0;
        for($i = 0; $i < sizeof($result); $i++) {
            $totalReservations += $result[$i][1];
            $totalAmount = $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Session\Stock')
                ->getProductAvailability($result[$i][0], $salesSession);
            $result[$i][2] = $totalAmount;
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'totals'            => $result,
                'salesSession'      => $salesSession,
                'totalReservations' => $totalReservations,
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

        $noShowConfig = $this->getNoShowConfig();

        // Get total amount of warnings for person (past, present and future)
        $warningCount = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation\Ban')
            ->findAllByPersonQuery($reservation->getPerson())
            ->getResult();

        // Create ban
        $banStartTimestamp = new DateTime();

        $banIntervalStr = $noShowConfig->getBanInterval($warningCount);
        $banInterval = DateInterval::createFromDateString($banIntervalStr);
        $banEndTimestamp = $banStartTimestamp->add($banInterval);

        $ban = new Ban();
        $ban->setPerson($reservation->getPerson());
        $ban->setStartTimestamp($banStartTimestamp);
        $ban->setEndTimestamp($banEndTimestamp);

        $this->getEntityManager()->persist($ban);
        $this->getEntityManager()->flush($ban);

        // Send warning email
        $mail = $noShowConfig->getEmail($reservation->getPerson(), $warningCount);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status'           => 'success',
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

        $winnerEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shop.enable_winner');

        $heading = array('Person', 'r-Number', 'Product', 'Amount','Total Price', 'Picked Up', $winnerEnabled ? 'Winner' : null);
        $results = array();
        foreach ($items as $item) {
            $results[] = array(
                $item->getPerson()->getFullName(),
                $item->getPerson()->getUniversityIdentification(),
                $item->getProduct()->getName(),
                (string) $item->getAmount(),
                (string) $item->getAmount() * $item->getProduct()->getSellPrice(),
                '',
                ''
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
            $item->consumed = $reservation->getConsumed();
            $item->reward = $reservation->getReward();

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

    private function getNoShowConfig()
    {
        $noShowConfigData = unserialize($this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shop.no_show_config'));

        $noShowConfig = new NoShowConfig($noShowConfigData);

        return $noShowConfig;
    }
}
