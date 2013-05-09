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

namespace LogisticsBundle\Controller;

use LogisticsBundle\Form\VanReservation\Add as AddForm,
    LogisticsBundle\Entity\Driver,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel,
    DateTime;

/**
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class IndexController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction()
    {
        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        return new ViewModel(
            array(
                'form' => $form,
                'date' => $this->getParam('date'),
            )
        );
    }

    public function moveAction()
    {
        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

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
                )
            )
        );
    }

    public function fetchAction()
    {
        $this->initAjax();

        $reservations = $this->_getReservations();

        if (null === $reservations) {
            return new ViewModel();
        }

        $result = array();
        foreach ($reservations as $reservation) {
            $driver = $reservation->getDriver();

            $driverArray = array(
                'color' => '#444444',
                'name' => ''
            );
            if (null !== $driver) {
                $driverArray['color'] = $driver->getColor();
                $driverArray['name'] = $driver->getPerson()->getFullname();
            }

            $passenger = $reservation->getPassenger();

            $passengerName = '';
            if (null !== $passenger)
                $passengerName = $passenger->getFullname();

            $additionalInfo = $reservation->getAdditionalInfo();

            $load = $reservation->getLoad();

            $result[] = array (
                'start' => $reservation->getStartDate()->getTimeStamp(),
                'end' => $reservation->getEndDate()->getTimeStamp(),
                'reason' => $reservation->getReason(),
                'driver' => $driverArray,
                'passenger' => $passengerName,
                'load' => $load,
                'additionalInfo' => $additionalInfo,
                'id' => $reservation->getId()
            );
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'reservations' => (object) $result
                )
            )
        );
    }

    private function _getReservations()
    {
        if (null === $this->getParam('start') || null === $this->getParam('end')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $startTime = new DateTime();
        $startTime->setTimeStamp($this->getParam('start'));

        $endTime = new DateTime();
        $endTime->setTimeStamp($this->getParam('end'));

        $reservations = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findAllByDates($startTime, $endTime);

        if (empty($reservations))
            $reservations = array();

        return $reservations;
    }

    private function _getReservation()
    {
        if (null === $this->getParam('id')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $reservation = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findOneById($this->getParam('id'));

        if (null == $reservation) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return $reservation;
    }
}
