<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateInterval,
    DateTime,
    IntlDateFormatter,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    LogisticsBundle\Form\PianoReservation\Add as AddForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PianoController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        if (!$this->getAuthentication()->isAuthenticated())
            return new ViewModel();

        $form = new AddForm($this->getEntityManager(), $this->getLanguage());

        $reservations = array();
        if ($this->getAuthentication()->isAuthenticated()) {
            $reservations = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                ->findAllByDatesAndPerson(
                    $this->getCurrentAcademicYear()->getUniversityStartDate(),
                    $this->getCurrentAcademicYear()->getUniversityEndDate(),
                    $this->getAuthentication()->getPersonObject()
                );
        }

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                foreach($form->getWeeks() as $key => $week) {
                    if (isset($formData['submit_' . $key])) {
                        $weekIndex = $key;
                        break;
                    }
                }

                if (isset($weekIndex)) {
                    $piano = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                        ->findOneByName(PianoReservation::PIANO_RESOURCE_NAME);

                    $reservation = new PianoReservation(
                        DateTime::createFromFormat('D d#m#Y H#i', $formData['start_date_' . $weekIndex]),
                        DateTime::createFromFormat('D d#m#Y H#i', $formData['end_date_' . $weekIndex]),
                        $piano,
                        '',
                        $this->getAuthentication()->getPersonObject(),
                        $this->getAuthentication()->getPersonObject()
                    );

                    $startWeek = new DateTime();
                    $startWeek->setISODate($reservation->getStartDate()->format('Y'), $weekIndex)
                        ->setTime(0, 0);
                    $endWeek = clone $startWeek;
                    $endWeek->add(new DateInterval('P7D'));

                    $otherReservations = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                        ->findAllConfirmedByDatesAndPerson($startWeek, $endWeek, $this->getAuthentication()->getPersonObject());

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

                    if ((sizeof($otherReservations) == 0 && $reservation->getStartDate() > $deadline) || $autoConfirm) {
                        $mailData = unserialize(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('logistics.piano_new_reservation_confirmed')
                        );
                        $reservation->setConfirmed();
                    } else {
                        $mailData = unserialize(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('logistics.piano_new_reservation')
                        );
                    }

                    if (!($language = $this->getAuthentication()->getPersonObject()->getLanguage())) {
                        $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
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
                    $mail->setBody(
                            str_replace('{{ name }}', $this->getAuthentication()->getPersonObject()->getFullName(),
                                str_replace('{{ start }}', $formatterDate->format($reservation->getStartDate()),
                                    str_replace('{{ end }}', $formatterDate->format($reservation->getEndDate()), $message)
                                )
                            )
                        )
                        ->setFrom(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('system_mail_address')
                        )
                        ->addTo($this->getAuthentication()->getPersonObject()->getEmail(), $this->getAuthentication()->getPersonObject()->getFullName())
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

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);

                    $this->getEntityManager()->persist($reservation);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'The reservation was succesfully created!'
                        )
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
        }

        return new ViewModel(
            array(
                'form' => $form,
                'reservations' => $reservations,
            )
        );
    }
}