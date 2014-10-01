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

namespace LogisticsBundle\Controller\Admin;

use DateTime,
    IntlDateFormatter,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

class PianoReservationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('logistics_piano-reservation_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $reservation = $form->hydrateObject();

                if ($reservation->isConfirmed()) {
                    $mailData = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('logistics.piano_new_reservation_confirmed')
                    );

                    if (!($language = $player->getLanguage())) {
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
                    $mail->setBody(
                            str_replace('{{ name }}', $player->getFullName(),
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
                        ->addTo($player->getEmail(), $player->getFullName())
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

                    if ('development' != getenv('APPLICATION_ENV')) {
                        $this->getMailTransport()->send($mail);
                    }
                }

                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The reservation was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_piano_reservation',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($reservation = $this->_getReservation())) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_piano-reservation_edit', array('reservation' => $reservation));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                if ($reservation->isConfirmed()) {
                    $mailData = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('logistics.piano_new_reservation_confirmed')
                    );

                    if (!($language = $player->getLanguage())) {
                        $language = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Language')
                            ->findOneByAbbrev('en');
                    }

                    $message = $mailData[$language->getAbbrev()]['content'];
                    $subject = $mailData[$language->getAbbrev()]['subject'];

                    $mail = new Message();
                    $mail->setBody(
                            str_replace('{{ name }}', $player->getFullName(),
                                str_replace('{{ start }}', $reservation->getStartDate()->format('D d/m/Y H:i'),
                                    str_replace('{{ end }}', $reservation->getEndDate()->format('D d/m/Y H:i'), $message)
                                )
                            )
                        )
                        ->setFrom(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('system_mail_address')
                        )
                        ->addTo($player->getEmail(), $player->getFullName())
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

                    if ('development' != getenv('APPLICATION_ENV')) {
                        $this->getMailTransport()->send($mail);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The reservation was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_piano_reservation',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getReservation()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the reservation!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_piano_reservation',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $reservation = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
            ->findOneById($this->getParam('id'));

        if (null === $reservation) {
            $this->flashMessenger()->error(
                'Error',
                'No article with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_piano_reservation',
                array(
                    'action' => 'manage',
                )
            );

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
        return DateTime::createFromFormat('D d#m#Y H#i', $date) ?: null;
    }
}
