<?php

namespace LogisticsBundle\Controller\Admin;

use IntlDateFormatter;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Reservation\Piano as PianoReservation;

class PianoReservationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Piano')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Piano')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
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

                    $language = $reservation->getPlayer()->getLanguage();
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
                                $reservation->getPlayer()->getFullName(),
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
                        ->addTo($reservation->getPlayer()->getEmail(), $reservation->getPlayer()->getFullName())
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
        $reservation = $this->getPianoReservationEntity();
        if ($reservation === null) {
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

                    $language = $reservation->getPlayer()->getLanguage();
                    if ($language === null) {
                        $language = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Language')
                            ->findOneByAbbrev('en');
                    }

                    $message = $mailData[$language->getAbbrev()]['content'];
                    $subject = $mailData[$language->getAbbrev()]['subject'];

                    $mail = new Message();
                    $mail->setEncoding('UTF-8')
                        ->setBody(
                            str_replace(
                                '{{ name }}',
                                $reservation->getPlayer()->getFullName(),
                                str_replace(
                                    '{{ start }}',
                                    $reservation->getStartDate()->format('D d/m/Y H:i'),
                                    str_replace('{{ end }}', $reservation->getEndDate()->format('D d/m/Y H:i'), $message)
                                )
                            )
                        )
                        ->setFrom(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('system_mail_address')
                        )
                        ->addTo($reservation->getPlayer()->getEmail(), $reservation->getPlayer()->getFullName())
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

        $reservation = $this->getPianoReservationEntity();
        if ($reservation === null) {
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

    /**
     * @return PianoReservation|null
     */
    private function getPianoReservationEntity()
    {
        $reservation = $this->getEntityById('LogisticsBundle\Entity\Reservation\Piano');

        if (!($reservation instanceof PianoReservation)) {
            $this->flashMessenger()->error(
                'Error',
                'No reservation was found!'
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
}
