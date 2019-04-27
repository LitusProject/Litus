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

namespace LogisticsBundle\Controller;

use CommonBundle\Entity\User\Person;
use DateInterval;
use DateTime;
use IntlDateFormatter;
use LogisticsBundle\Entity\Reservation\Piano as PianoReservation;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PianoController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->notFoundAction();
        }

        $form = $this->getForm('logistics_piano-reservation_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $startDate = null;
            $endDate = null;
            foreach (array_keys($form->getWeeks()) as $key) {
                if (isset($formData['week_' . $key]['submit'])) {
                    $weekIndex = $key;

                    $startDate = self::loadDate($formData['week_' . $key]['start_date']);
                    $endDate = self::loadDate($formData['week_' . $key]['end_date']);
                    break;
                }
            }

            if ($form->isValid() && isset($weekIndex) && $startDate && $endDate) {
                $piano = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Reservation\Resource')
                    ->findOneByName(PianoReservation::RESOURCE_NAME);

                $reservation = new PianoReservation(
                    $piano,
                    $person
                );

                $reservation->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setPlayer($person);

                $startWeek = new DateTime();
                $startWeek->setISODate($reservation->getStartDate()->format('Y'), $weekIndex, 1)
                    ->setTime(0, 0);
                $endWeek = clone $startWeek;
                $endWeek->add(new DateInterval('P7D'));

                $otherReservations = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Reservation\Piano')
                    ->findAllConfirmedByDatesAndPerson($startWeek, $endWeek, $person);

                $deadline = new DateTime();
                $deadline->add(
                    new DateInterval(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('logistics.piano_auto_confirm_deadline')
                    )
                );

                $autoConfirm = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.piano_auto_confirm_immediatly');

                if ((count($otherReservations) == 0 && $reservation->getStartDate() > $deadline) || $autoConfirm) {
                    $reservation->setConfirmed();
                }

                $this->sendMail($reservation, $person);

                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The reservation was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'logistics_piano',
                    array(
                        'action' => 'index',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'         => $form,
                'reservations' => $this->getReservations(),
            )
        );
    }

    /**
     * @return Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return array
     */
    private function getReservations()
    {
        $person = $this->getPersonEntity();
        if ($person !== null) {
            return $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Piano')
                ->findAllByDatesAndPerson(
                    $this->getCurrentAcademicYear()->getUniversityStartDate(),
                    $this->getCurrentAcademicYear()->getUniversityEndDate(),
                    $person
                );
        }

        return array();
    }

    /**
     * @param PianoReservation $reservation
     */
    private function sendMail(PianoReservation $reservation, Person $person)
    {
        if ($reservation->isConfirmed()) {
            $mailData = unserialize(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.piano_new_reservation_confirmed')
            );
        } else {
            $mailData = unserialize(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.piano_new_reservation')
            );
        }

        $language = $person->getLanguage();
        if ($language === null) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $formatterDate = new IntlDateFormatter(
            $language->getAbbrev(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'EEE d/MM/Y HH:mm'
        );

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(
                str_replace(
                    '{{ name }}',
                    $person->getFullName(),
                    str_replace(
                        '{{ start }}',
                        $formatterDate->format($reservation->getStartDate()),
                        str_replace('{{ end }}', $formatterDate->format($reservation->getEndDate()), $message)
                    )
                )
            )
            ->setFrom(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_mail_address')
            )
            ->addTo($person->getEmail(), $person->getFullName())
            ->addTo(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.piano_mail_to')
            )
            ->addBcc(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_administrator_mail'),
                'System Administrator'
            )
            ->setSubject($subject);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }

    /**
     * @param  string $date
     * @return DateTime|null
     */
    private static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
