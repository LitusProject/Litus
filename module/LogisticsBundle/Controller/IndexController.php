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

namespace LogisticsBundle\Controller;

use CommonBundle\Component\Util\File\TmpFile as TmpFile,
    DateTime,
    LogisticsBundle\Component\Document\Generator\Ics as IcsGenerator,
    LogisticsBundle\Document\Token,
    LogisticsBundle\Entity\Driver,
    LogisticsBundle\Entity\Reservation\ReservableResource,
    LogisticsBundle\Entity\Reservation\VanReservation,
    LogisticsBundle\Form\VanReservation\Add as AddForm,
    LogisticsBundle\Form\VanReservation\Edit as EditForm,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class IndexController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction()
    {
        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        $token = null;
        if ($this->getAuthentication()->isAuthenticated()) {
            $token = $this->getDocumentManager()
                ->getRepository('LogisticsBundle\Document\Token')
                ->findOneByPerson($this->getAuthentication()->getPersonObject());
        }

        if (null === $token && $this->getAuthentication()->isAuthenticated()) {
            $token = new Token(
                $this->getAuthentication()->getPersonObject()
            );
            $this->getDocumentManager()->persist($token);
            $this->getDocumentManager()->flush();
        }

        return new ViewModel(
            array(
                'form' => $form,
                'date' => $this->getParam('date'),
                'token' => $token,
            )
        );
    }

    public function addAction()
    {
        $this->initAjax();

        if ($this->getRequest()->isPost()) {
            $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $startDate = self::_loadDate($formData['start_date']);
            $endDate = self::_loadDate($formData['end_date']);

            if ($form->isValid() && $startDate && $endDate) {
                $driver = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Driver')
                    ->findOneById($formData['driver']);

                if ('' == $formData['passenger_id']) {
                    $passenger = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($formData['passenger']);
                } else {
                    $passenger = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['passenger_id']);
                }

                $van = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                    ->findOneByName(VanReservation::VAN_RESOURCE_NAME);

                if (null === $van) {
                    $van = new ReservableResource(VanReservation::VAN_RESOURCE_NAME);
                    $this->getEntityManager()->persist($van);
                }

                $reservation = new VanReservation(
                    $startDate,
                    $endDate,
                    $formData['reason'],
                    $formData['load'],
                    $van,
                    $formData['additional_info'],
                    $this->getAuthentication()->getPersonObject()
                );

                if (null !== $driver) {
                    $reservation->setDriver($driver);
                }

                if (null !== $passenger) {
                    $reservation->setPassenger($passenger);
                }

                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                $driverArray = array(
                    'color' => '#444444',
                    'name' => '',
                );
                if (null !== $driver) {
                    $driverArray['id'] = $driver->getPerson()->getId();
                    $driverArray['color'] = $driver->getColor();
                    $driverArray['name'] = $driver->getPerson()->getFullname();
                }

                $passenger = $reservation->getPassenger();

                $passengerName = '';
                $passengerId = '';
                if (null !== $passenger) {
                    $passengerName = $passenger->getFullname();
                    $passengerId = $passenger->getId();
                }

                $result = array (
                    'start' => $reservation->getStartDate()->getTimeStamp(),
                    'end' => $reservation->getEndDate()->getTimeStamp(),
                    'reason' => $reservation->getReason(),
                    'driver' => $driverArray,
                    'passenger' => $passengerName,
                    'passengerId' => $passengerId,
                    'load' => $reservation->getLoad(),
                    'additionalInfo' => $reservation->getAdditionalInfo(),
                    'id' => $reservation->getId(),
                );

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'success',
                            'reservation' => $result,
                        ),
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $key => $element) {
                    if (!isset($errors[$element->getName()])) {
                        continue;
                    }

                    $formErrors[$element->getAttribute('id')] = array();

                    foreach ($errors[$element->getName()] as $error) {
                        $formErrors[$element->getAttribute('id')][] = $error;
                    }
                }

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'errors' => $formErrors,
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

        if (!($reservation = $this->_getReservation())) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->isPost()) {
            $form = new EditForm($this->getEntityManager(), $this->getCurrentAcademicYear(), $reservation);
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $startDate = self::_loadDate($formData['start_date']);
            $endDate = self::_loadDate($formData['end_date']);

            if ($form->isValid() && $startDate && $endDate) {
                $driver = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Driver')
                    ->findOneById($formData['driver']);

                if ('' == $formData['passenger_id']) {
                    $passenger = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($formData['passenger']);
                } else {
                    $passenger = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['passenger_id']);
                }

                $reservation->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setReason($formData['reason'])
                    ->setLoad($formData['load'])
                    ->setAdditionalInfo($formData['additional_info']);

                if (null !== $driver) {
                    $reservation->setDriver($driver);
                }

                if (null !== $passenger) {
                    $reservation->setPassenger($passenger);
                }

                $this->getEntityManager()->flush();

                $driverArray = array(
                    'color' => '#444444',
                    'name' => '',
                );
                if (null !== $driver) {
                    $driverArray['id'] = $driver->getPerson()->getId();
                    $driverArray['color'] = $driver->getColor();
                    $driverArray['name'] = $driver->getPerson()->getFullname();
                }

                $passenger = $reservation->getPassenger();

                $passengerName = '';
                $passengerId = '';
                if (null !== $passenger) {
                    $passengerName = $passenger->getFullname();
                    $passengerId = $passenger->getId();
                }

                $result = array (
                    'start' => $reservation->getStartDate()->getTimeStamp(),
                    'end' => $reservation->getEndDate()->getTimeStamp(),
                    'reason' => $reservation->getReason(),
                    'driver' => $driverArray,
                    'passenger' => $passengerName,
                    'passengerId' => $passengerId,
                    'load' => $reservation->getLoad(),
                    'additionalInfo' => $reservation->getAdditionalInfo(),
                    'id' => $reservation->getId(),
                );

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'success',
                            'reservation' => $result,
                        ),
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $key => $element) {
                    if (!isset($errors[$element->getName()])) {
                        continue;
                    }

                    $formErrors[$element->getAttribute('id')] = array();

                    foreach ($errors[$element->getName()] as $error) {
                        $formErrors[$element->getAttribute('id')][] = $error;
                    }
                }

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'errors' => $formErrors,
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

        if (!($reservation = $this->_getReservation())) {
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
        if (!($reservation = $this->_getReservation())) {
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

        $reservations = $this->_getReservations();

        if (null === $reservations) {
            return $this->notFoundAction();
        }

        $result = array();
        foreach ($reservations as $reservation) {
            $driver = $reservation->getDriver();

            $driverArray = array(
                'color' => '#444444',
                'name' => '',
            );
            if (null !== $driver) {
                $driverArray['id'] = $driver->getPerson()->getId();
                $driverArray['color'] = $driver->getColor();
                $driverArray['name'] = $driver->getPerson()->getFullname();
            }

            $passenger = $reservation->getPassenger();

            $passengerName = '';
            $passengerId = '';
            if (null !== $passenger) {
                $passengerName = $passenger->getFullname();
                $passengerId = $passenger->getId();
            }

            $result[] = array (
                'start' => $reservation->getStartDate()->getTimeStamp(),
                'end' => $reservation->getEndDate()->getTimeStamp(),
                'reason' => $reservation->getReason(),
                'driver' => $driverArray,
                'passenger' => $passengerName,
                'passengerId' => $passengerId,
                'load' => $reservation->getLoad(),
                'additionalInfo' => $reservation->getAdditionalInfo(),
                'id' => $reservation->getId(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'reservations' => (object) $result,
                ),
            )
        );
    }

    public function exportAction()
    {
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="icalendar.ics"',
            'Content-Type' => 'text/calendar',
        ));
        $this->getResponse()->setHeaders($headers);

        $icsFile = new TmpFile();
        $icsGenerator = new IcsGenerator($icsFile, $this->getEntityManager(), $this->getDocumentManager(), $this->getParam('token'));

        return new ViewModel(
            array(
                'result' => $icsFile->getContent(),
            )
        );
    }

    private function _getReservations()
    {
        if (null === $this->getParam('start') || null === $this->getParam('end')) {
            return;
        }

        $startTime = new DateTime();
        $startTime->setTimeStamp($this->getParam('start'));

        $endTime = new DateTime();
        $endTime->setTimeStamp($this->getParam('end'));

        $reservations = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findAllByDates($startTime, $endTime);

        if (empty($reservations)) {
            $reservations = array();
        }

        return $reservations;
    }

    /**
     * @return VanReservation
     */
    private function _getReservation()
    {
        if (null === $this->getParam('id')) {
            return;
        }

        $reservation = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findOneById($this->getParam('id'));

        if (null == $reservation) {
            return;
        }

        return $reservation;
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
