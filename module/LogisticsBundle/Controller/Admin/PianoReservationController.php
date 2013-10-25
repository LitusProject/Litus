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

namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    LogisticsBundle\Form\Admin\PianoReservation\Add as AddForm,
    LogisticsBundle\Form\Admin\PianoReservation\Edit as EditForm,
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
        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');

                $player = ('' == $formData['player_id'])
                    ? $repository->findOneByUsername($formData['player']) : $repository->findOneById($formData['player_id']);

                $piano = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                    ->findOneByName(PianoReservation::PIANO_RESOURCE_NAME);

                $reservation = new PianoReservation(
                    DateTime::createFromFormat('D d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('D d#m#Y H#i', $formData['end_date']),
                    $piano,
                    $formData['additional_info'],
                    $this->getAuthentication()->getPersonObject(),
                    $player
                );

                $reservation->setConfirmed(isset($formData['confirmed']) && $formData['confirmed']);

                if ($reservation->isConfirmed()) {
                    $mailData = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('logistics.piano_new_reservation_confirmed')
                    );

                    if (!($language = $player->getLanguage())) {
                        $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
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

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);
                }

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
        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $reservation);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');

                $player = ('' == $formData['player_id'])
                    ? $repository->findOneByUsername($formData['player']) : $repository->findOneById($formData['player_id']);

                $reservation->setStartDate(DateTime::createFromFormat('D d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('D d#m#Y H#i', $formData['end_date']))
                    ->setAdditionalInfo($formData['additional_info'])
                    ->setPlayer($player)
                    ->setConfirmed(isset($formData['confirmed']) && $formData['confirmed']);

                if ($reservation->isConfirmed()) {
                    $mailData = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('logistics.piano_new_reservation_confirmed')
                    );

                    if (!($language = $player->getLanguage())) {
                        $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
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

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The reservation was successfully updated!'
                    )
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

        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

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
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the reservation!'
                )
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
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
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