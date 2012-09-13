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

use LogisticsBundle\Entity\Driver,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    LogisticsBundle\Form\Admin\Driver\Add,
    Zend\View\Model\ViewModel,
    \DateTime,
    LogisticsBundle\Form\Admin\Driver\Edit;

/**
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class IndexController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction()
    {
        return new ViewModel();
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
                'name' => 'None'
            );
            if (null !== $driver) {
                $driverArray['color'] = $driver->getColor();
                $driverArray['name'] = $driver->getPerson()->getFullname();
            }

            $passenger = $reservation->getPassenger();

            $passengerName = 'None';
            if (null !== $passenger)
                $passengerName = $passenger->getFullname();

            $additionalInfo = $reservation->getAdditionalInfo();
            if ('' == $additionalInfo)
                $additionalInfo = 'None';

            $result[] = array (
                'start' => $reservation->getStartDate()->getTimeStamp(),
                'end' => $reservation->getEndDate()->getTimeStamp(),
                'reason' => $reservation->getReason(),
                'driver' => $driverArray,
                'passenger' => $passengerName,
                'load' => $reservation->getLoad(),
                'additionalInfo' => $additionalInfo,
                'id' => $reservation->getId()
            );
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                    'reservations' => $result
                )
            )
        );

    }

    private function _getReservations()
    {
        if (null === $this->getParam('start') || null === $this->getParam('end')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No start or end date was given to identify the reservations!'
                )
            );

            // @TODO probably should not redirect to the page that causes the problem
            $this->redirect()->toRoute(
                'logistics_index',
                array(
                    'action' => 'index'
                )
            );

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
}