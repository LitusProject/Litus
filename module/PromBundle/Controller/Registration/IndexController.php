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
namespace PromBundle\Controller\Registration;

use PromBundle\Entity\Bus\Passenger,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Mathijs Cuppens
 */
class IndexController extends \PromBundle\Component\Controller\RegistrationController
{
    public function registrationAction()
    {
        $createForm = $this->getForm('prom_registration_create');
        $manageForm = $this->getForm('prom_registration_manage');

        $enable = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('prom.enable_reservations');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['create'])) {
                $createForm->setData($formData);

                if ($createForm->isValid()) {
                    $createFormData = $createForm->getData();

                    $codeExist = $this->getEntityManager()
                        ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                        ->codeExist($createFormData['create']['ticket_code']);

                    if (! $codeExist) {
                        return new ViewModel(
                            array(
                                'enable'     => $enable,
                                'createForm' => $createForm,
                                'manageForm' => $manageForm,
                                'status'     => 'fail_code',
                            )
                        );
                    }

                    $code = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                            ->getRegistrationCodeByCode($createFormData['create']['ticket_code']);

                    $passenger = $this->getEntityManager()
                        ->getRepository('PromBundle\Entity\Bus\Passenger')
                        ->findPassengerByCodeQuery($code)->getResult();

                    if ($codeExist && !$code->isUsed() && !isset($passenger[0])) {
                        setcookie('VTK_bus_code', $createFormData['create']['ticket_code'], time() + 3600, '/');

                        $this->redirect()->toRoute(
                            'prom_registration_index',
                            array(
                                'action' => 'create',
                            )
                        );
                    } else {
                        return new ViewModel(
                            array(
                                'enable'     => $enable,
                                'createForm' => $createForm,
                                'manageForm' => $manageForm,
                                'status'     => 'fail_code',
                            )
                        );
                    }
                }
            } elseif (isset($formData['manage'])) {
                $manageForm->setData($formData);
                if ($manageForm->isValid()) {
                    $manageFormData = $manageForm->getData();
                    $codeExist = $this->getEntityManager()
                        ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                        ->codeExist($manageFormData['manage']['ticket_code']);
                    if ($codeExist) {
                        $code = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                            ->getRegistrationCodeByCode($manageFormData['manage']['ticket_code']);
                        $passenger = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus\Passenger')
                            ->findPassengerByCodeQuery($code)->getResult()[0];
                        $correctEmail = $manageFormData['manage']['email'] == $passenger->getEmail();

                        if ($correctEmail) {
                            setcookie('VTK_bus_code', $manageFormData['manage']['ticket_code'], time() + 3600, '/');

                            $this->redirect()->toRoute(
                                'prom_registration_index',
                                array(
                                    'action' => 'manage',
                                )
                            );
                        } else {
                            return new ViewModel(
                                array(
                                    'enable'     => $enable,
                                    'createForm' => $createForm,
                                    'manageForm' => $manageForm,
                                    'status'     => 'fail_combination',
                                )
                            );
                        }
                    } else {
                        return new ViewModel(
                            array(
                                'enable'     => $enable,
                                'createForm' => $createForm,
                                'manageForm' => $manageForm,
                                'status'     => 'fail_code',
                            )
                        );
                    }
                }
            }
        }

        return new ViewModel(
            array(
                'enable'     => $enable,
                'createForm' => $createForm,
                'manageForm' => $manageForm,
            )
        );
    }

    public function createAction()
    {
        $addForm = $this->getForm('prom_registration_add');
        $addForm->setData(array(
            'ticket_code' => $_COOKIE['VTK_bus_code'],
        ));

        $code = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getRegistrationCodeByCode($_COOKIE['VTK_bus_code']);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData)) {
                $addForm->setData($formData);
                if ($addForm->isValid()) {
                    $firstBus = null;
                    $secondBus = null;
                    if ($formData['first_bus'] != 0) {
                        $firstBus = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus')
                            ->findOneById($formData['first_bus']);
                    }
                    if ($formData['second_bus'] != 0) {
                        $secondBus = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus')
                            ->findOneById($formData['second_bus']);
                    }
                    $passenger = $this->getEntityManager()
                        ->getRepository('PromBundle\Entity\Bus\Passenger')
                        ->findOneByEmail($formData['email']);

                    if (!isset($passenger)) {
                        $newPassenger = new Passenger($formData['first_name'], $formData['last_name'], $formData['email'], $code, $firstBus, $secondBus);

                        $this->getEntityManager()->persist($newPassenger);
                        $code->setUsed();
                        $this->getEntityManager()->flush();

                        if (isset($firstBus)) {
                            $mail = new Message();

                            $mail->addTo($newPassenger->getEmail());

                            $mailData = unserialize(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\General\Config')
                                    ->getConfigValue('prom.confirmation_mail')
                            );

                            $mail->setBody(str_replace('{{ busTime }}', $firstBus->getDepartureTime()->format('d/m/Y H:i'), $mailData['body']))
                                ->setFrom($mailData['from'])
                                ->addBcc($mailData['from'])
                                ->setSubject($mailData['subject']);

                            if ('development' != getenv('APPLICATION_ENV')) {
                                $this->getMailTransport()->send($mail);
                            }
                        }

                        if (isset($secondBus)) {
                            $mail = new Message();

                            $mail->addTo($newPassenger->getEmail());

                            $mailData = unserialize(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\General\Config')
                                    ->getConfigValue('prom.confirmation_mail')
                            );

                            $mail->setBody(str_replace('{{ busTime }}', $secondBus->getDepartureTime()->format('d/m/Y H:i'), $mailData['body']))
                                ->setFrom($mailData['from'])
                                ->addBcc($mailData['from'])
                                ->setSubject($mailData['subject']);

                            if ('development' != getenv('APPLICATION_ENV')) {
                                $this->getMailTransport()->send($mail);
                            }
                        }

                        $this->redirect()->toRoute(
                            'prom_registration_index',
                            array(
                                'status' => 'success_registration',
                            )
                        );
                    } else {
                        return new ViewModel(
                            array(
                                'addForm' => $addForm,
                                'status' => 'invalid_email',
                            )
                        );
                    }
                }
            }
        }
        if (! $code->isUsed()) {
            return new ViewModel(
                array(
                    'addForm' => $addForm,
                )
            );
        } else {
            return new ViewModel(
                array(
                    'status' => 'used_code',
                )
            );
        }
    }

    public function manageAction()
    {
        $editForm = $this->getForm('prom_registration_edit',
            array('passenger' => 'test')
        );

        $code = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getRegistrationCodeByCode($_COOKIE['VTK_bus_code']);

        $passenger = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\Passenger')
            ->findPassengerByCodeQuery($code)->getResult()[0];

        $originalFirstBus = $passenger->getFirstBus();
        $originalSecondBus = $passenger->getSecondBus();
        $firstBusId = (isset($originalFirstBus) ? $originalFirstBus->getId() : 0);
        $secondBusId = (isset($originalSecondBus) ? $originalSecondBus->getId() : 0);

        $editForm->setData(array(
            'ticket_code' => $passenger->getCode()->getCode(),
            'email'      => $passenger->getEmail(),
            'first_name'  => $passenger->getFirstName(),
            'last_name'      => $passenger->getLastName(),
            'first_bus'   => $firstBusId,
            'second_bus'  => $secondBusId,
        ));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData)) {
                $editForm->setData($formData);
                if ($editForm->isValid()) {
                    $firstBus = null;
                    $secondBus = null;
                    if ($formData['first_bus'] != 0) {
                        $firstBus = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus')
                            ->findOneById($formData['first_bus']);
                        if ( !($originalFirstBus == $firstBus) && (($firstBus->getTotalSeats() - $firstBus->getReservedSeats()) <= 0) ) {
                            return new ViewModel(
                                array(
                                    'editForm' => $editForm,
                                    'status'   => 'no_seats_left',
                                )
                            );
                        }
                    }
                    if ($formData['second_bus'] != 0) {
                        $secondBus = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus')
                            ->findOneById($formData['second_bus']);
                        if ( !($originalSecondBus == $secondBus) && (($secondBus->getTotalSeats() - $secondBus->getReservedSeats()) <= 0) ) {
                            return new ViewModel(
                                array(
                                    'editForm' => $editForm,
                                    'status'   => 'no_seats_left',
                                )
                            );
                        }
                    }
                    $code = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                            ->getRegistrationCodeByCode($_COOKIE['VTK_bus_code']);

                    $passenger = $this->getEntityManager()
                            ->getRepository('PromBundle\Entity\Bus\Passenger')
                            ->findOneByCode($code);

                    $passenger->setFirstBus($firstBus);
                    $passenger->setSecondBus($secondBus);

                    $this->getEntityManager()->persist($passenger);
                    $this->getEntityManager()->flush();

                    $this->redirect()->toRoute(
                        'prom_registration_index',
                        array(
                            'status' => 'success_manage',
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'editForm' => $editForm,
            )
        );
    }
}
