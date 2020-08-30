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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Controller;

use DateInterval;
use DateTime;
use ShiftBundle\Entity\Shift\Registered;
use ShiftBundle\Entity\Token;
use Zend\Http\Headers;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

/**
 * RegistrationShiftController
 *
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RegistrationShiftController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $eventSearchForm = $this->getForm('shift_registrationShift_search_event', array('language' => $this->getLanguage()));
        $unitSearchForm = $this->getForm('shift_registrationShift_search_unit');
        $dateSearchForm = $this->getForm('shift_registrationShift_search_date');

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->notFoundAction();
        }

        $myShifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\RegistrationShift')
            ->findAllActiveByPerson($person);

        $token = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Token')
            ->findOneByPerson($person);

        if ($token === null) {
            $token = new Token($person);

            $this->getEntityManager()->persist($token);
            $this->getEntityManager()->flush();
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
                        ->getRepository('ShiftBundle\Entity\RegistrationShift')
                        ->findAllActiveByEvent($event);

                    $resultString = $this->getTranslator()->translate('Registration shifts for %event%');
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
                        ->getRepository('ShiftBundle\Entity\RegistrationShift')
                        ->findAllActiveByUnit($unit);

                    $resultString = $this->getTranslator()->translate('Registration shifts for %unit%');
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
                            ->getRepository('ShiftBundle\Entity\RegistrationShift')
                            ->findAllActiveBetweenDates($start_date, $end_date);

                        $resultString = $this->getTranslator()->translate('Registration shifts from %start% to %end%');
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
                ->getRepository('ShiftBundle\Entity\RegistrationShift')
                ->findAllActiveBetweenDates($start_date, $end_date);

            $resultString = $this->getTranslator()->translate('Registration shifts from %start% to %end%');
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

        return new ViewModel(
            array(
                'resultString'     => $resultString,
                'eventSearchForm'  => $eventSearchForm,
                'unitSearchForm'   => $unitSearchForm,
                'dateSearchForm'   => $dateSearchForm,
                'myShifts'         => $myShifts,
                'token'            => $token,
                'searchResults'    => $searchResults,
                'entityManager'    => $this->getEntityManager(),
            )
        );
    }

    public function RegisteredAction()
    {
        $this->initAjax();

        $shift = $this->getRegistrationShiftEntity();
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

        if (!$shift->canHaveAsRegistered($this->getEntityManager(), $person)) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        if ($shift->countRegistered() >= $shift->getNbRegistered()) {
            foreach (array_reverse($shift->getRegistered()) as $registered) {
                if ($registered->getPerson()->isPraesidium($this->getCurrentAcademicYear())) {

                    $mailAddress = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail');

                    $mailName = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail_name');

                    $language = $registered->getPerson()->getLanguage();
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

                    $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

                    $mail = new Message();
                    $mail->setEncoding('UTF-8')
                        ->setBody(str_replace('{{ shift }}', $shiftString, $message))
                        ->setFrom($mailAddress, $mailName)
                        ->addTo($registered->getPerson()->getEmail(), $registered->getPerson()->getFullName())
                        ->setSubject($subject);

                    if (getenv('APPLICATION_ENV') != 'development') {
                        $this->getMailTransport()->send($mail);
                    }

                    $this->getEntityManager()->remove($registered);
                    break;
                }
            }
        }

        $shift->addRegistered(
            $this->getEntityManager(),
            new Registered(
                $person
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio'  => $shift->getNbRegistered() == 0 ? 0 : $shift->countRegistered() / $shift->getNbRegistered(),
                ),
            )
        );
    }

    public function signOutAction()
    {
        $this->initAjax();

        $shift = $this->getRegistrationShiftEntity();
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

        $remove = $shift->removeRegistered($person);
        if ($remove !== null) {
            $this->getEntityManager()->remove($remove);
        }


        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'ratio'  => $shift->getNbRegistered() == 0 ? 0 : $shift->countRegistered() / $shift->getNbRegistered(),
                ),
            )
        );
    }

    public function historyAction()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->notFoundAction();
        }

        $academicYear = $this->getCurrentAcademicYear(true);

        $asRegistered = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\RegistrationShift')
            ->findAllByPersonAsRegistered($person, $academicYear);

        $hoursPerBlock = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.hours_per_shift');

        $now = new DateTime();

        $shiftsAsRegistered = array();
        $shiftsAsRegisteredCount = 0;
        $lastShift = new DateTime('2000-01-01');
        foreach ($asRegistered as $shift) {
            if ($shift->getStartDate() > $now) {
                continue;
            }

            if ($shift->getEndDate() > $lastShift) {
                $lastShift = $shift->getEndDate();
            }

            if (!isset($shiftsAsRegistered[$shift->getUnit()->getId()])) {
                $shiftsAsRegistered[$shift->getUnit()->getId()] = array(
                    'count'    => 1,
                    'unitName' => $shift->getUnit()->getName(),
                );
            } else {
                $shiftsAsRegistered[$shift->getUnit()->getId()]['count']++;
            }

            $shiftsAsRegisteredCount++;

            if ($hoursPerBlock > 0) {
                $hoursOverTime = date_diff($shift->getStartDate(), $shift->getEndDate())->format('%h') - $hoursPerBlock;
                if ($hoursOverTime > 0) {
                    $amoutOfBlocks = floor($hoursOverTime / $hoursPerBlock);
                    $shiftsAsRegistered[$shift->getUnit()->getId()]['count'] += $amoutOfBlocks;
                    $shiftsAsRegisteredCount += $amoutOfBlocks;
                }
            }
        }

        $praesidium = $person->isPraesidium($academicYear);

        return new ViewModel(
            array(
                'shiftsAsRegistered'   => $shiftsAsRegistered,
                'totalAsRegistered'    => $shiftsAsRegisteredCount,
                'lastShift'           => $lastShift->format('d/m/Y'),
                'praesidium'          => $praesidium,
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
     * @return \ShiftBundle\Entity\RegistrationShift|null
     */
    private function getRegistrationShiftEntity()
    {
        return $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\RegistrationShift')
            ->findOneById($this->getRequest()->getPost('id', 0));
    }
}
