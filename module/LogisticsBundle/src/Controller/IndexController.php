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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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

            if ($driver === null) {
                $driverName = "None";
                $driverColor = "#444444";
            } else {
                $driverName = $driver->getPerson()->getFullname();
                $driverColor = $driver->getColor();
            }

            $passenger = $reservation->getPassenger();

            if ($passenger === null) {
                $passengerName = "None";
            } else {
                $passengerName = $passenger->getFullname();
            }

            $result[] = array (
                'reason' => $reservation->getReason(),
                'start' => $reservation->getStartDate()->getTimeStamp(),
                'end' => $reservation->getEndDate()->getTimeStamp(),

                'driver' => array(
                    'color' => $driverColor,
                    'name' => $driverName,
                ),
                'passenger' => $passengerName,
                'load' => $reservation->getLoad(),
                'additional' => $reservation->getAdditionalInfo(),
                'id' => $reservation->getId(),

            );
        }

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success", "reservations" => (object) $result),
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

            // TODO probably should not redirect to the page that causes the problem
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

        if (null === $reservations) {
            // If no reservations are found, return an empty array
            $reservations = array();
        }

        return $reservations;
    }

}