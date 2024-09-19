<?php

namespace LogisticsBundle\Controller;

use CommonBundle\Component\Util\File\TmpFile;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Component\Document\Generator\Ics as IcsGenerator;
use LogisticsBundle\Entity\Reservation\Van as VanReservation;
use LogisticsBundle\Entity\Token;

/**
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class TransportController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction()
    {
        $form = $this->getForm('logistics_van-reservation_add');

        $token = null;
        if ($this->getAuthentication()->isAuthenticated()) {
            $token = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Token')
                ->findOneByPerson($this->getAuthentication()->getPersonObject());
        }

        if ($token === null && $this->getAuthentication()->isAuthenticated()) {
            $token = new Token(
                $this->getAuthentication()->getPersonObject()
            );

            $this->getEntityManager()->persist($token);
            $this->getEntityManager()->flush();
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'date'  => $this->getParam('date'),
                'token' => $token,                                  // Token is used to generate dropdown for export
                                                                    // (download) button so car drivers have the choice
                                                                    // to download a personal or general calendar
            )
        );
    }

    public function addAction()
    {
        $this->initAjax();

        if ($this->getRequest()->isPost()) {
            $form = $this->getForm('logistics_van-reservation_add');
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $reservation = $form->hydrateObject();
                $driver = $reservation->getDriver();
                $passenger = $reservation->getPassenger();

                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                $driverArray = array(
                    'color' => '#444444',
                    'name'  => '',
                );
                if ($driver !== null) {
                    $driverArray['id'] = $driver->getPerson()->getId();
                    $driverArray['color'] = $driver->getColor();
                    $driverArray['name'] = $driver->getPerson()->getFullname();
                }

                $passengerName = '';
                $passengerId = '';
                if ($passenger !== null) {
                    $passengerName = $passenger->getFullname();
                    $passengerId = $passenger->getId();
                }

                $result = array (
                    'start'          => $reservation->getStartDate()->getTimeStamp(),
                    'end'            => $reservation->getEndDate()->getTimeStamp(),
                    'reason'         => $reservation->getReason(),
                    'driver'         => $driverArray,
                    'passenger'      => $passengerName,
                    'passengerId'    => $passengerId,
                    'load'           => $reservation->getLoad(),
                    'car'            => $reservation->getCar(),
                    'bike'           => $reservation->getBike(),
                    'additionalInfo' => $reservation->getAdditionalInfo(),
                    'id'             => $reservation->getId(),
                );

                return new ViewModel(
                    array(
                        'result' => array(
                            'status'      => 'success',
                            'reservation' => $result,
                        ),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'error',
                ),
            )
        );
    }

    public function editAction()
    {
        $this->initAjax();

        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->isPost()) {
            $form = $this->getForm('logistics_van-reservation_add', array('reservation' => $reservation));
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $driver = $reservation->getDriver();

                $driverArray = array(
                    'color' => '#444444',
                    'name'  => '',
                );
                if ($driver !== null) {
                    $driverArray['id'] = $driver->getPerson()->getId();
                    $driverArray['color'] = $driver->getColor();
                    $driverArray['name'] = $driver->getPerson()->getFullname();
                }

                $passenger = $reservation->getPassenger();

                $passengerName = '';
                $passengerId = '';
                if ($passenger !== null) {
                    $passengerName = $passenger->getFullname();
                    $passengerId = $passenger->getId();
                }

                $result = array (
                    'start'          => $reservation->getStartDate()->getTimeStamp(),
                    'end'            => $reservation->getEndDate()->getTimeStamp(),
                    'reason'         => $reservation->getReason(),
                    'driver'         => $driverArray,
                    'passenger'      => $passengerName,
                    'passengerId'    => $passengerId,
                    'load'           => $reservation->getLoad(),
                    'car'            => $reservation->getCar(),
                    'bike'           => $reservation->getBike(),
                    'additionalInfo' => $reservation->getAdditionalInfo(),
                    'id'             => $reservation->getId(),
                );

                return new ViewModel(
                    array(
                        'result' => array(
                            'status'      => 'success',
                            'reservation' => $result,
                        ),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'error',
                ),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return $this->notFoundAction();
        }

        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array('status' => 'success'),
            )
        );
    }

    public function moveAction()
    {
        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return $this->notFoundAction();
        }

        $start = new DateTime();
        $start->setTimeStamp($this->getRequest()->getPost('start'));
        $end = new DateTime();
        $end->setTimeStamp($this->getRequest()->getPost('end'));

        $reservation->setStartDate($start)
            ->setEndDate($end);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function fetchAction()
    {
        $this->initAjax();

        $reservations = $this->getReservations();
        if ($reservations === null) {
            return $this->notFoundAction();
        }

        $result = array();
        foreach ($reservations as $reservation) {
            $driver = $reservation->getDriver();

            $driverArray = array(
                'color' => '#444444',
                'name'  => '',
            );
            if ($driver !== null) {
                $driverArray['id'] = $driver->getPerson()->getId();
                $driverArray['color'] = $driver->getColor();
                $driverArray['name'] = $driver->getPerson()->getFullname();
            }

            $passenger = $reservation->getPassenger();

            $passengerName = '';
            $passengerId = '';
            if ($passenger !== null) {
                $passengerName = $passenger->getFullname();
                $passengerId = $passenger->getId();
            }

            $result[] = array (
                'start'          => $reservation->getStartDate()->getTimeStamp(),
                'end'            => $reservation->getEndDate()->getTimeStamp(),
                'reason'         => $reservation->getReason(),
                'driver'         => $driverArray,
                'passenger'      => $passengerName,
                'passengerId'    => $passengerId,
                'load'           => $reservation->getLoad(),
                'car'            => $reservation->getCar(),
                'bike'           => $reservation->getBike(),
                'additionalInfo' => $reservation->getAdditionalInfo(),
                'id'             => $reservation->getId(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status'       => 'success',
                    'reservations' => (object) $result,
                ),
            )
        );
    }

    public function exportAction()
    {
        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'inline; filename="icalendar.ics"',
                'Content-Type'        => 'text/calendar',
            )
        );
        $this->getResponse()->setHeaders($headers);

        $icsFile = new TmpFile();
        new IcsGenerator($icsFile, $this->getEntityManager(), $this->getParam('token'));

        return new ViewModel(
            array(
                'result' => $icsFile->getContent(),
            )
        );
    }

    /**
     * @return array|null
     */
    private function getReservations()
    {
        if ($this->getParam('start') === null || $this->getParam('end') === null) {
            return;
        }

        $startTime = new DateTime();
        $startTime->setTimeStamp($this->getParam('start'));

        $endTime = new DateTime();
        $endTime->setTimeStamp($this->getParam('end'));

        $reservations = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\Van')
            ->findAllByDates($startTime, $endTime);

        if (count($reservations) == 0) {
            $reservations = array();
        }

        return $reservations;
    }

    /**
     * @return VanReservation|null
     */
    private function getVanReservationEntity()
    {
        $reservation = $this->getEntityById('LogisticsBundle\Entity\Reservation\Van');

        if (!($reservation instanceof VanReservation)) {
            return;
        }

        return $reservation;
    }
}
