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

namespace ShiftBundle\Controller;

use CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    ShiftBundle\Document\Token,
    ShiftBundle\Entity\Shift\Responsible,
    ShiftBundle\Entity\Shift\Volunteer,
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
        $eventSearchForm = $this->getForm('shift_shift_search_event', array('language' => $this->getLanguage()));
        $unitSearchForm = $this->getForm('shift_shift_search_unit');
        $dateSearchForm = $this->getForm('shift_shift_search_date');

        if (!$this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->warn(
                'Warning',
                'Please login to view the shifts!'
            );

            $this->redirect()->toRoute(
                'common_index',
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
                    $formData = $eventSearchForm->getData();

                    $event = $this->getEntityManager()
                        ->getRepository('CalendarBundle\Entity\Node\Event')
                        ->findOneById($formData['event']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByEvent($event);

                    $resultString = $this->getTranslator()->translate('Shifts for %event%');
                    $resultString = str_replace('%event%', $event->getTitle(), $resultString);
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'The given search query was invalid!'
                    );
                }
            }

            if (isset($formData['unit'])) {
                $unitSearchForm->setData($formData);

                if ($unitSearchForm->isValid() && '' != $formData['unit']) {
                    $formData = $unitSearchForm->getData();

                    $unit = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                        ->findOneById($formData['unit']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByUnit($unit);

                    $resultString = $this->getTranslator()->translate('Shifts for %unit%');
                    $resultString = str_replace('%unit%', $unit->getName(), $resultString);
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'The given search query was invalid!'
                    );
                }
            }

            if (isset($formData['date'])) {
                $dateSearchForm->setData($formData);

                if ($dateSearchForm->isValid() && '' != $formData['date']) {
                    $formData = $dateSearchForm->getData();

                    $start_date = DateTime::createFromFormat('d/m/Y' , $formData['date']);
                    if (!$start_date) {
                        $this->flashMessenger()->error(
                            'Error',
                            'The given search query was invalid; please enter a date in the format dd/mm/yyyy!'
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
                    $this->flashMessenger()->error(
                        'Error',
                        'The given search query was invalid!'
                    );
                }
            }
        } else {
            $start_date = new DateTime();
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

        $academicYear = $this->getCurrentAcademicYear();
        $now = new DateTime();
        if ($now < $academicYear->getUniversityStartDate() && $now > $academicYear->getStartDate())
            $searchResults = array();

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

                    if (!($language = $volunteer->getPerson()->getLanguage())) {
                        $language = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Language')
                            ->findOneByAbbrev('en');
                    }

                    $mailData = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('shift.praesidium_removed_mail')
                    );

                    $message = $mailData[$language->getAbbrev()]['content'];
                    $subject = $mailData[$language->getAbbrev()]['subject'];

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

        $payed = false;
        if ($shift->getHandledOnEvent())
            $payed = true;

        $shift->addVolunteer(
            $this->getEntityManager(),
            new Volunteer(
                $person,
                $payed
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio' => $shift->getNbVolunteers() == 0 ? 0 : $shift->countVolunteers() / $shift->getNbVolunteers()
                )
            )
        );
    }

    public function signOutAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()) || !($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error')
                )
            );
        }

        if (!($shift->canSignOut($this->getEntityManager()))) {
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
                    'ratio' => $shift->getNbVolunteers() == 0 ? 0 : $shift->countVolunteers() / $shift->getNbVolunteers()
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
            ->getConfigValue('shift.icalendar_uid_suffix');

        $result = 'BEGIN:VCALENDAR' . PHP_EOL;
        $result .= 'VERSION:2.0' . PHP_EOL;
        $result .= 'X-WR-CALNAME:' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name') . ' My Shift Calendar' . PHP_EOL;
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

            foreach ($shifts as $shift) {
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

    public function historyAction()
    {
        $academicYear = $this->getCurrentAcademicYear(true);

        $asVolunteer = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsVolunteer($this->getAuthentication()->getPersonObject(), $academicYear);

        $asResponsible = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsReponsible($this->getAuthentication()->getPersonObject(), $academicYear);

        $now = new DateTime();

        $shiftsAsVolunteer = array();
        $shiftsAsVolunteerCount = 0;
        $unPayedShifts = 0;
        $unPayedCoins = 0;
        $lastShift = new DateTime();
        foreach ($asVolunteer as $shift) {
            if ($shift->getStartDate() > $now)
                continue;

            //if ($shift->getEndDate() > $lastShift)
                $lastShift = $shift->getEndDate();

            if (!isset($shiftsAsVolunteer[$shift->getUnit()->getId()])) {
                $shiftsAsVolunteer[$shift->getUnit()->getId()] = array(
                    'count' => 1,
                    'unitName' => $shift->getUnit()->getName()
                );
            } else {
                $shiftsAsVolunteer[$shift->getUnit()->getId()]['count']++;
            }

            $shiftsAsVolunteerCount++;
            foreach ($shift->getVolunteers() as $volunteer) {
                if ($volunteer->getPerson() == $this->getAuthentication()->getPersonObject() && !($volunteer->isPayed())) {
                    $unPayedShifts += 1;
                    $unPayedCoins += $shift->getReward();
                }
            }
        }

        $shiftsAsResponsible = array();
        $shiftsAsResponsibleCount = 0;
        foreach ($asResponsible as $shift) {
            if ($shift->getStartDate() > $now)
                continue;

            if (!isset($shiftsAsResponsible[$shift->getUnit()->getId()])) {
                $shiftsAsResponsible[$shift->getUnit()->getId()] = array(
                    'count' => 1,
                    'unitName' => $shift->getUnit()->getName()
                );
            } else {
                $shiftsAsResponsible[$shift->getUnit()->getId()]['count']++;
            }

            $shiftsAsResponsibleCount++;
        }

        return new ViewModel(
            array(
                'shiftsAsVolunteer' => $shiftsAsVolunteer,
                'totalAsVolunteer' => $shiftsAsVolunteerCount,
                'shiftsAsResponsible' => $shiftsAsResponsible,
                'totalAsResponsible' => $shiftsAsResponsibleCount,
                'unPayedShifts' => $unPayedShifts,
                'unPayedCoins' => $unPayedCoins,
                'lastShift' => $lastShift->format('d/m/Y')
            )
        );
    }

    /**
     * @return \ShiftBundle\Entity\Shift|null
     */
    private function _getShift()
    {
        if (null === $this->getRequest()->getPost('id'))
            return null;

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getRequest()->getPost('id'));

        return $shift;
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function _getPerson()
    {
        if (null === $this->getRequest()->getPost('person'))
            return null;

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->getRequest()->getPost('person'));

        return $person;
    }
}
