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

namespace ShiftBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    DateTime,
    DateInterval,
    ShiftBundle\Document\Token,
    ShiftBundle\Entity\Shifts\Responsible,
    ShiftBundle\Entity\Shifts\Volunteer,
    ShiftBundle\Form\Shift\Search\Date as DateSearchForm,
    ShiftBundle\Form\Shift\Search\Event as EventSearchForm,
    ShiftBundle\Form\Shift\Search\Unit as UnitSearchForm,
    Zend\Http\Headers,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * Flight Mode
 * This file was edited by Pieter Maene while in flight from Vienna to Brussels
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $eventSearchForm = new EventSearchForm($this->getEntityManager(), $this->getLanguage());
        $unitSearchForm = new UnitSearchForm($this->getEntityManager());
        $dateSearchForm = new DateSearchForm($this->getEntityManager());

        if (!$this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::WARNING,
                    'Warning',
                    'Please log in to view the shifts!'
                )
            );

            $this->redirect()->toRoute(
                'index',
                array(
                    'action' => 'index'
                )
            );

            return new ViewModel();
        }

        $myShifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());

        $token = $this->getDocumentManager()
            ->getRepository('ShiftBundle\Document\Token')
            ->findOneByPerson($this->getAuthentication()->getPersonObject());

        if (null === $token) {
            $token = new Token(
                $this->getAuthentication()->getPersonObject()
            );
            $this->getDocumentManager()->persist($token);
            $this->getDocumentManager()->flush();
        }

        $searchResults = null;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['event'])) {
                $eventSearchForm->setData($formData);

                if ($eventSearchForm->isValid() && '' != $formData['event']) {
                    $formData = $eventSearchForm->getFormData($formData);

                    $event = $this->getEntityManager()
                        ->getRepository('CalendarBundle\Entity\Nodes\Event')
                        ->findOneById($formData['event']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByEvent($event);

                    $resultString = $this->getTranslator()->translate('Shifts for %event%');
                    $resultString = str_replace('%event%', $event->getTitle(), $resultString);
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }

            if (isset($formData['unit'])) {
                $unitSearchForm->setData($formData);

                if ($unitSearchForm->isValid() && '' != $formData['unit']) {
                    $formData = $unitSearchForm->getFormData($formData);

                    $unit = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Unit')
                        ->findOneById($formData['unit']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByUnit($unit);

                    $resultString = $this->getTranslator()->translate('Shifts for %unit%');
                    $resultString = str_replace('%unit%', $unit->getName(), $resultString);
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }

            if (isset($formData['date'])) {
                $dateSearchForm->setData($formData);

                if ($dateSearchForm->isValid() && '' != $formData['date']) {
                    $formData = $dateSearchForm->getFormData($formData);

                    $start_date = DateTime::createFromFormat('d/m/Y' , $formData['date']);
                    if(!$start_date) {
                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::ERROR,
                                'Error',
                                'The given search query was invalid; please enter a date in the format dd/mm/yyyy!'
                            )
                        );
                    } else {
                        $start_date->setTime(0, 0, 0);
                        $end_date = clone $start_date;
                        $end_date->add(new DateInterval('P1W'));

                        $searchResults = $this->getEntityManager()
                            ->getRepository('ShiftBundle\Entity\Shift')
                            ->findAllActiveBetweenDates($start_date, $end_date);

                        $resultString = $this->getTranslator()->translate('Shifts from %start% to %end%');
                        $resultString = str_replace('%start%', $start_date->format('d/m/Y'), $resultString);
                        $resultString = str_replace('%end%', $end_date->format('d/m/Y'), $resultString);
                    }
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }
        } else {
            $start_date = new DateTime();
            $start_date->setTime(0, 0, 0);
            $end_date = clone $start_date;
            $end_date->add(new DateInterval('P1W'));

            $searchResults = $searchResults = $this->getEntityManager()
                            ->getRepository('ShiftBundle\Entity\Shift')
                            ->findAllActiveBetweenDates($start_date, $end_date);

            $resultString = $this->getTranslator()->translate('Shifts from %start% to %end%');
            $resultString = str_replace('%start%', $start_date->format('d/m/Y'), $resultString);
            $resultString = str_replace('%end%', $end_date->format('d/m/Y'), $resultString);
        }

        if (!isset($resultString))
            $resultString = 'Results';

        if (null !== $searchResults) {
            foreach ($myShifts as $shift) {
                if (in_array($shift, $searchResults))
                    unset($searchResults[array_keys($searchResults, $shift)[0]]);
            }
        }

        return new ViewModel(
            array(
                'resultString' => $resultString,
                'eventSearchForm' => $eventSearchForm,
                'unitSearchForm' => $unitSearchForm,
                'dateSearchForm' => $dateSearchForm,
                'myShifts' => $myShifts,
                'token' => $token,
                'searchResults' => $searchResults,
                'entityManager' => $this->getEntityManager()
            )
        );
    }

    public function responsibleAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()) || !($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if (!($shift->canHaveAsResponsible($this->getEntityManager(), $person))) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        $shift->addResponsible(
            $this->getEntityManager(),
            new Responsible(
                $person,
                $this->getCurrentAcademicYear()
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio' => $shift->countResponsibles() / $shift->getNbResponsibles()
                )
            )
        );
    }

    public function volunteerAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()) || !($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if (!($shift->canHaveAsVolunteer($this->getEntityManager(), $person))) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if ($shift->countVolunteers() >= $shift->getNbVolunteers()) {
            foreach (array_reverse($shift->getVolunteers()) as $volunteer) {
                if ($volunteer->getPerson()->isPraesidium($this->getCurrentAcademicYear())) {
                    $shift->removeVolunteer($volunteer);

                    $mailAddress = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail');

                    $mailName = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail_name');

                    $message = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.praesidium_removed_mail');

                    $subject = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.praesidium_removed_mail_subject');

                    $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

                    $mail = new Message();
                    $mail->setBody(str_replace('{{ shift }}', $shiftString, $message))
                        ->setFrom($mailAddress, $mailName)
                        ->addTo($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName())
                        ->setSubject($subject);

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);

                    $this->getEntityManager()->remove($volunteer);
                    break;
                }
            }
        }

        $shift->addVolunteer(
            $this->getEntityManager(),
            new Volunteer(
                $person
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio' => $shift->countVolunteers() / $shift->getNbVolunteers()
                )
            )
        );
    }

    public function signoutAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()) || !($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if (!($shift->canSignout($this->getEntityManager(), $person))) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        $remove = $shift->removePerson($person);

        if (null !== $remove)
            $this->getEntityManager()->remove($remove);

        /**
         * @TODO If a responsible signs out, and there's another praesidium member signed up as a volunteer, promote him
         */

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio' => $shift->countVolunteers() / $shift->getNbVolunteers()
                )
            )
        );
    }

    public function exportAction()
    {
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="icalendar.ics"',
            'Content-type' => 'text/calendar',
        ));
        $this->getResponse()->setHeaders($headers);

        $suffix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.icalendar_uid_suffix');

        $result = 'BEGIN:VCALENDAR' . PHP_EOL;
        $result .= 'VERSION:2.0' . PHP_EOL;
        $result .= 'X-WR-CALNAME:' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('union_short_name') . ' My Shift Calendar' . PHP_EOL;
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

        if (null !== $this->getParam('token')) {
            $token = $this->getDocumentManager()
                ->getRepository('ShiftBundle\Document\Token')
                ->findOneByHash($this->getParam('token'));

            $shifts = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllActiveByPerson($token->getPerson($this->getEntityManager()));

            foreach($shifts as $shift) {
                $result .= 'BEGIN:VEVENT' . PHP_EOL;
                $result .= 'SUMMARY:' . $shift->getName() . PHP_EOL;
                $result .= 'DTSTART:' . $shift->getStartDate()->format('Ymd\THis') . PHP_EOL;
                $result .= 'DTEND:' . $shift->getEndDate()->format('Ymd\THis') . PHP_EOL;
                $result .= 'TRANSP:OPAQUE' . PHP_EOL;
                $result .= 'LOCATION:' . $shift->getLocation()->getName() . PHP_EOL;
                $result .= 'CLASS:PUBLIC' . PHP_EOL;
                $result .= 'UID:' . $shift->getId() . '@' . $suffix . PHP_EOL;
                $result .= 'END:VEVENT' . PHP_EOL;
            }
        }

        $result .= 'END:VCALENDAR';

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _getShift()
    {
        if (null === $this->getRequest()->getPost('id'))
            return null;

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getRequest()->getPost('id'));

        return $shift;
    }

    private function _getPerson()
    {
        if (null === $this->getRequest()->getPost('person'))
            return null;

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Person')
            ->findOneById($this->getRequest()->getPost('person'));

        return $person;
    }
}