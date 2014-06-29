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

use DateTime,
    LogisticsBundle\Form\VanReservation\Add as AddForm,
    LogisticsBundle\Form\VanReservation\Edit as EditForm,
    LogisticsBundle\Document\Token,
    LogisticsBundle\Entity\Driver,
    LogisticsBundle\Entity\Reservation\ReservableResource,
    LogisticsBundle\Entity\Reservation\VanReservation,
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

                if (null !== $driver)
                    $reservation->setDriver($driver);

                if (null !== $passenger)
                    $reservation->setPassenger($passenger);

                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                $driverArray = array(
                    'color' => '#444444',
                    'name' => ''
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
                    'id' => $reservation->getId()
                );

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'success',
                            'reservation' => $result,
                        )
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $key => $element) {
                    if (!isset($errors[$element->getName()]))
                        continue;

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
                        )
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'error',
                )
            )
        );
    }

    public function editAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return $this->notFoundAction();

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

                if (null !== $driver)
                    $reservation->setDriver($driver);

                if (null !== $passenger)
                    $reservation->setPassenger($passenger);

                $this->getEntityManager()->flush();

                $driverArray = array(
                    'color' => '#444444',
                    'name' => ''
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
                    'id' => $reservation->getId()
                );

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'success',
                            'reservation' => $result,
                        )
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $key => $element) {
                    if (!isset($errors[$element->getName()]))
                        continue;

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
                        )
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'error',
                )
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return $this->notFoundAction();

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
        if (!($reservation = $this->_getReservation()))
            return $this->notFoundAction();

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
            return $this->notFoundAction();
        }

        $result = array();
        foreach ($reservations as $reservation) {
            $driver = $reservation->getDriver();

            $driverArray = array(
                'color' => '#444444',
                'name' => ''
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

    public function exportAction()
    {
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="icalendar.ics"',
            'Content-Type' => 'text/calendar',
        ));
        $this->getResponse()->setHeaders($headers);

        $suffix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.icalendar_uid_suffix');

        $result = 'BEGIN:VCALENDAR' . PHP_EOL;
        $result .= 'VERSION:2.0' . PHP_EOL;
        $result .= 'X-WR-CALNAME:' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name') . ' Logistics' . PHP_EOL;
        $result .= 'PRODID:-//lituscal//NONSGML v1.0//EN' . PHP_EOL;
        $result .= 'CALSCALE:GREGORIAN' . PHP_EOL;
        $result .= 'METHOD:PUBLISH' . PHP_EOL;
        $result .= 'X-WR-TIMEZONE:Europe/Brussels' . PHP_EOL;
        $result .= 'BEGIN:VTIMEZONE' . PHP_EOL;
        $result .= 'TZID:Europe/Brussels' . PHP_EOL;
        $result .= 'X-LIC-LOCATION:Europe/Brussels' . PHP_EOL;
        $result .= 'BEGIN:DAYLIGHT' . PHP_EOL;
        $result .= 'TZOFFSETFROM:+0100' . PHP_EOL;
        $result .= 'TZOFFSETTO:+0200' . PHP_EOL;
        $result .= 'TZNAME:CEST' . PHP_EOL;
        $result .= 'DTSTART:19700329T020000' . PHP_EOL;
        $result .= 'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU' . PHP_EOL;
        $result .= 'END:DAYLIGHT' . PHP_EOL;
        $result .= 'BEGIN:STANDARD' . PHP_EOL;
        $result .= 'TZOFFSETFROM:+0200' . PHP_EOL;
        $result .= 'TZOFFSETTO:+0100' . PHP_EOL;
        $result .= 'TZNAME:CET' . PHP_EOL;
        $result .= 'DTSTART:19701025T030000' . PHP_EOL;
        $result .= 'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU' . PHP_EOL;
        $result .= 'END:STANDARD' . PHP_EOL;
        $result .= 'END:VTIMEZONE' . PHP_EOL;

        $reservations = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findAllActive();

        $person = null;
        if (null !== $this->getParam('token')) {
            $token = $this->getDocumentManager()
                ->getRepository('LogisticsBundle\Document\Token')
                ->findOneByHash($this->getParam('token'));

            if (null !== $token)
                $person = $token->getPerson($this->getEntityManager());
        }

        foreach ($reservations as $reservation) {
            if (null !== $person && $reservation->getDriver() && $reservation->getDriver()->getPerson() != $person)
                continue;

            $summary = array();
            if (strlen($reservation->getLoad()) > 0)
                $summary[] = str_replace("\n", '', $reservation->getLoad());
            if (strlen($reservation->getAdditionalInfo()) > 0)
                $summary[] = str_replace("\n", '', $reservation->getAdditionalInfo());

            $result .= 'BEGIN:VEVENT' . PHP_EOL;
            $result .= 'SUMMARY:' . $reservation->getReason() . PHP_EOL;
            $result .= 'DTSTART:' . $reservation->getStartDate()->format('Ymd\THis') . PHP_EOL;
            $result .= 'DTEND:' . $reservation->getEndDate()->format('Ymd\THis') . PHP_EOL;
            if ($reservation->getDriver())
                $result .= 'ORGANIZER;CN="' . $reservation->getDriver()->getPerson()->getFullname() . '":MAILTO:' . $reservation->getDriver()->getPerson()->getEmail() . PHP_EOL;
            if ($reservation->getPassenger())
                $result .= 'ATTENDEE;CN="' . $reservation->getPassenger()->getFullname() . '":MAILTO:' . $reservation->getPassenger()->getEmail() . PHP_EOL;
            $result .= 'DESCRIPTION:' . implode(' - ', $summary) . PHP_EOL;
            $result .= 'TRANSP:OPAQUE' . PHP_EOL;
            $result .= 'CLASS:PUBLIC' . PHP_EOL;
            $result .= 'UID:' . $reservation->getId() . '@' . $suffix . PHP_EOL;
            $result .= 'END:VEVENT' . PHP_EOL;
        }

        $result .= 'END:VCALENDAR';

        return new ViewModel(
            array(
                'result' => $result,
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

        if (empty($reservations))
            $reservations = array();

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
