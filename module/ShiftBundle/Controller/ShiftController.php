<?php

namespace ShiftBundle\Controller;

use DateInterval;
use DateTime;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use ShiftBundle\Entity\Shift\Responsible;
use ShiftBundle\Entity\Shift\Volunteer;
use ShiftBundle\Entity\Token;
use ShiftBundle\Entity\User\Person\AcademicYearMap;

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

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->redirect()->toRoute('common_auth',
                array(
                'redirect' => urlencode($this->getRequest()->getRequestUri()),
            ));
        }

        $myShifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($person);

        $token = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Token')
            ->findOneByPerson($person);

        if ($token === null) {
            $token = new Token($person);

            $this->getEntityManager()->persist($token);
            $this->getEntityManager()->flush();
        }

        $insuranceEnabled = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.insurance_enabled')
        );
        $insuranceText = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.insurance_text')
        );

        $hasReadInsurance = true;
        if ($insuranceEnabled) {
            $mapping = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\User\Person\AcademicYearMap')
                ->findOneByPersonAndAcademicYear($person, $this->getCurrentAcademicYear());

            if ($mapping === null || !$mapping->hasReadInsurance()) {
                $hasReadInsurance = false;
            }
        }

        $searchResults = null;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['event'])) {
                $eventSearchForm->setData($formData);

                if ($eventSearchForm->isValid() && $formData['event'] != '') {
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
            } elseif (isset($formData['unit'])) {
                $unitSearchForm->setData($formData);

                if ($unitSearchForm->isValid() && $formData['unit'] != '') {
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
            } elseif (isset($formData['date'])) {
                $dateSearchForm->setData($formData);

                if ($dateSearchForm->isValid() && $formData['date'] != '') {
                    $formData = $dateSearchForm->getData();

                    $start_date = DateTime::createFromFormat('d/m/Y', $formData['date']);
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

        if (!isset($resultString)) {
            $resultString = 'Results';
        }

        if ($searchResults !== null) {
            foreach ($myShifts as $shift) {
                if (in_array($shift, $searchResults)) {
                    unset($searchResults[array_keys($searchResults, $shift)[0]]);
                }
            }
        }

        $currentAcademicYear = $this->getCurrentAcademicYear();

        return new ViewModel(
            array(
                'resultString'        => $resultString,
                'eventSearchForm'     => $eventSearchForm,
                'unitSearchForm'      => $unitSearchForm,
                'dateSearchForm'      => $dateSearchForm,
                'myShifts'            => $myShifts,
                'token'               => $token,
                'searchResults'       => $searchResults,
                'entityManager'       => $this->getEntityManager(),
                'currentAcademicYear' => $currentAcademicYear,
                'hasReadInsurance'    => $hasReadInsurance,
                'insuranceText'       => $insuranceText[$this->getLanguage()->getAbbrev()],
                'insuranceEnabled'    => $insuranceEnabled,
            )
        );
    }

    public function responsibleAction()
    {
        $this->initAjax();

        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $person = $this->getPersonEntity();
        if ($person === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        if (!$shift->canHaveAsResponsible($this->getEntityManager(), $person)) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $insuranceEnabled = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.insurance_enabled')
        );

        if ($insuranceEnabled) {
            $mapping = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\User\Person\AcademicYearMap')
                ->findOneByPersonAndAcademicYear($person, $this->getCurrentAcademicYear());

            if ($mapping === null) {
                $mapping = new AcademicYearMap($person, $this->getCurrentAcademicYear(), true);
            } elseif (!$mapping->hasReadInsurance()) {
                $mapping->setHasReadInsurance(true);
            }

            $this->getEntityManager()->persist($mapping);
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
                    'ratio'  => $shift->countResponsibles() / $shift->getNbResponsibles(),
                ),
            )
        );
    }

    public function volunteerAction()
    {
        $this->initAjax();

        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $person = $this->getPersonEntity();
        if ($person === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        if (!$shift->canHaveAsVolunteer($this->getEntityManager(), $person)) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
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

                    $language = $volunteer->getPerson()->getLanguage();
                    if ($language === null) {
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

                    $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y H:i') . ' to ' . $shift->getEndDate()->format('d/m/Y H:i');

                    $mail = new Message();
                    $mail->setEncoding('UTF-8')
                        ->setBody(str_replace('{{ shift }}', $shiftString, $message))
                        ->setFrom($mailAddress, $mailName)
                        ->addTo($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName())
                        ->setSubject($subject);

                    if (getenv('APPLICATION_ENV') != 'development') {
                        $this->getMailTransport()->send($mail);
                    }

                    $this->getEntityManager()->remove($volunteer);
                    break;
                }
            }
        }

        $payed = false;
        if ($shift->getHandledOnEvent()) {
            $payed = true;
        }

        $volunteers = $shift->getVolunteers();
        $persons = array();
        foreach ($volunteers as $vol) {
            $persons[] = $vol->getPerson()->getId();
        }

        if (!in_array($person->getId(), $persons)) {
            $shift->addVolunteer(
                $this->getEntityManager(),
                new Volunteer(
                    $person,
                    $payed
                )
            );
            $this->getEntityManager()->flush(); 
        }

        $shifter = $shift->getVolunteers();
        $count = 0;
        $personen = array();
        foreach ($shifter as $vol) {
            $personen[] = $vol->getPerson()->getId();
        }

        foreach ($personen as $per) {
            if ($per == $person->getId()) {
                $count++;
            }
        }
        while ($count > 1) {
            $remove = $this->getShiftEntity()->removePerson($person);
            if ($remove !== null) {
                $this->getEntityManager()->remove($remove);
            }
            $count--;
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio'  => $shift->getNbVolunteers() == 0 ? 0 : $shift->countVolunteers() / $shift->getNbVolunteers(),
                ),
            )
        );
    }

    public function signOutAction()
    {
        $this->initAjax();

        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $person = $this->getPersonEntity();
        if ($person === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        if (!$shift->canSignOut($this->getEntityManager())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $remove = $shift->removePerson($person);
        if ($remove !== null) {
            $this->getEntityManager()->remove($remove);
        }

        // TODO: If a responsible signs out, and there's another praesidium member signed up as a volunteer, promote him

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio'  => $shift->getNbVolunteers() == 0 ? 0 : $shift->countVolunteers() / $shift->getNbVolunteers(),
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

        if ($this->getParam('token') !== null) {
            $token = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Token')
                ->findOneByHash($this->getParam('token'));

            $shifts = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllActiveByPerson($token->getPerson());

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
        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->notFoundAction();
        }

        $insuranceEnabled = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.insurance_enabled')
        );

        $academicYear = $this->getCurrentAcademicYear(true);

        $asVolunteer = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsVolunteer($person, $academicYear);

        $asResponsible = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsReponsible($person, $academicYear);

        $hoursPerBlock = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.hours_per_shift');

        $points_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.points_enabled');

        $now = new DateTime();

        $shiftsAsVolunteer = array();
        $shiftsAsVolunteerCount = 0;
        $pointsAsVolunteerCount = 0;
        $unPayedShifts = 0;
        $unPayedCoins = 0;
        $lastShift = new DateTime('2000-01-01');
        foreach ($asVolunteer as $shift) {
            if ($shift->getStartDate() > $now) {
                continue;
            }

            if ($shift->getEndDate() > $lastShift) {
                $lastShift = $shift->getEndDate();
            }

            if (!isset($shiftsAsVolunteer[$shift->getUnit()->getId()])) {
                $shiftsAsVolunteer[$shift->getUnit()->getId()] = array(
                    'count'    => 1,
                    'unitName' => $shift->getUnit()->getName(),
                );
            } else {
                $shiftsAsVolunteer[$shift->getUnit()->getId()]['count']++;
            }

            $shiftsAsVolunteerCount++;
            $pointsAsVolunteerCount += $shift->getPoints();

            if ($hoursPerBlock > 0) {
                $hoursOverTime = date_diff($shift->getStartDate(), $shift->getEndDate())->format('%h') - $hoursPerBlock;
                if ($hoursOverTime > 0) {
                    $amoutOfBlocks = floor($hoursOverTime / $hoursPerBlock);
                    $shiftsAsVolunteer[$shift->getUnit()->getId()]['count'] += $amoutOfBlocks;
                    $shiftsAsVolunteerCount += $amoutOfBlocks;
                }
            }

            foreach ($shift->getVolunteers() as $volunteer) {
                if ($volunteer->getPerson() == $person && !$volunteer->isPayed()) {
                    $unPayedShifts++;
                    $unPayedCoins += $shift->getReward();
                }
            }
        }

        $rankingCriteria = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.ranking_criteria')
        );

        $ranking = false;
        $stepsToNextRanking = 0;
        $stepsAsVolunteerCount = 0;

        if ($points_enabled) {
            $stepsAsVolunteerCount = $pointsAsVolunteerCount;
        } else {
            $stepsAsVolunteerCount = $shiftsAsVolunteerCount;
        }

        if (count($rankingCriteria) > 0) {
            $stepsToNextRanking = $rankingCriteria[0]['limit'] - $stepsAsVolunteerCount;

            for ($i = 0; isset($rankingCriteria[$i]); $i++) {
                if ($rankingCriteria[$i]['limit'] <= $stepsAsVolunteerCount) {
                    $ranking = $rankingCriteria[$i]['name'];

                    if (isset($rankingCriteria[$i + 1])) {
                        $stepsToNextRanking = $rankingCriteria[$i + 1]['limit'] - $stepsAsVolunteerCount;
                    } else {
                        $stepsToNextRanking = 0;
                    }
                }
            }
        }

        $shiftsAsResponsible = array();
        $shiftsAsResponsibleCount = 0;
        foreach ($asResponsible as $shift) {
            if ($shift->getStartDate() > $now) {
                continue;
            }

            if (!isset($shiftsAsResponsible[$shift->getUnit()->getId()])) {
                $shiftsAsResponsible[$shift->getUnit()->getId()] = array(
                    'count'    => 1,
                    'unitName' => $shift->getUnit()->getName(),
                );
            } else {
                $shiftsAsResponsible[$shift->getUnit()->getId()]['count']++;
            }

            $shiftsAsResponsibleCount++;
        }

        $praesidium = $person->isPraesidium($academicYear);

        return new ViewModel(
            array(
                'shiftsAsVolunteer'   => $shiftsAsVolunteer,
                'totalAsVolunteer'    => $shiftsAsVolunteerCount,
                'shiftsAsResponsible' => $shiftsAsResponsible,
                'totalAsResponsible'  => $shiftsAsResponsibleCount,
                'unPayedShifts'       => $unPayedShifts,
                'unPayedCoins'        => $unPayedCoins,
                'lastShift'           => $lastShift->format('d/m/Y'),
                'praesidium'          => $praesidium,
                'ranking'             => $ranking,
                'stepsToNextRanking'  => $stepsToNextRanking,
                'insuranceEnabled'    => $insuranceEnabled,
                'points_enabled'      => $points_enabled,
            )
        );
    }

    public function insuranceAction()
    {
        $insuranceText = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.insurance_text')
        );

        $insuranceEnabled = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.insurance_enabled')
        );

        return new ViewModel(
            array(
                'insuranceText'    => $insuranceText[$this->getLanguage()->getAbbrev()],
                'insuranceEnabled' => $insuranceEnabled,
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return \ShiftBundle\Entity\Shift|null
     */
    private function getShiftEntity()
    {
        return $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getRequest()->getPost('id', 0));
    }
}
