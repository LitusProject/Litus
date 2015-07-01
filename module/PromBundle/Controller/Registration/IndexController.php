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

use CommonBundle\Component\Form\Form,
    PromBundle\Entity\Bus,
    PromBundle\Entity\Bus\Passenger,
    PromBundle\Entity\Bus\ReservationCode,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Mathijs Cuppens
 * @author Kristof Marien <kristof.marien@litus.cc>
 */
class IndexController extends \PromBundle\Component\Controller\RegistrationController
{
    private static $cookieNamespace = 'Litus_Bus_Code';

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
                    $this->manageCreateForm($createForm);
                }
            } elseif (isset($formData['manage'])) {
                $manageForm->setData($formData);

                if ($manageForm->isValid()) {
                    $this->manageManageForm($manageForm);
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
        if (!($code = $this->getReservationCodeEntity())) {
            return new ViewModel();
        }

        $addForm = $this->getForm('prom_registration_add', array('code' => $code));

        if ($this->getRequest()->isPost()) {
            $addForm->setData($this->getRequest()->getPost());

            if ($addForm->isValid()) {
                $formData = $addForm->getData();

                $firstBus = $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus')
                    ->findOneById($formData['first_bus']);

                $secondBus = $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus')
                    ->findOneById($formData['second_bus']);

                $passenger = new Passenger($formData['first_name'], $formData['last_name'], $formData['email'], $code, $firstBus, $secondBus);
                $code->setUsed();

                $this->getEntityManager()->persist($passenger);
                $this->getEntityManager()->flush();

                if (isset($firstBus)) {
                    $this->sendConfirmationMail($passenger, $firstBus);
                }

                if (isset($secondBus)) {
                    $this->sendConfirmationMail($passenger, $secondBus);
                }

                $this->flashMessenger()->success(
                    'Success',
                    'You have successfully registered your buses.'
                );

                $this->redirect()->toRoute(
                    'prom_registration_index',
                    array(
                        'action' => 'registration',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'addForm' => $addForm,
            )
        );
    }

    public function manageAction()
    {
        if (!($code = $this->getReservationCodeEntity(false))) {
            return new ViewModel();
        }

        $passengers = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\Passenger')
            ->findPassengerByCode($code);

        $passenger = null;
        if (sizeof($passengers) > 0) {
            $passenger = $passengers[0];
        }

        $editForm = $this->getForm('prom_registration_edit', array('passenger' => $passenger));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $editForm->setData($formData);

            if ($editForm->isValid()) {
                $firstBus = $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus')
                    ->findOneById($formData['first_bus']);
                $secondBus = $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus')
                    ->findOneById($formData['second_bus']);

                $passenger->setFirstBus($firstBus);
                $passenger->setSecondBus($secondBus);

                $this->getEntityManager()->persist($passenger);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'You have successfully registered your buses.'
                );

                $this->redirect()->toRoute(
                    'prom_registration_index',
                    array(
                        'action' => 'registration',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'editForm' => $editForm,
            )
        );
    }

    /**
     * @param  Form $form
     * @return null
     */
    private function manageCreateForm(Form $form)
    {
        $createFormData = $form->getData();

        $code = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getRegistrationCodeByCode($createFormData['create']['ticket_code']);

        if (null !== $code) {
            setcookie(
                self::$cookieNamespace,
                $code->getCode(),
                time() + 3600,
                '/'
            );

            $this->redirect()->toRoute(
                'prom_registration_index',
                array(
                    'action' => 'create',
                )
            );
        }
    }

    /**
     * @param  Form $form
     * @return null
     */
    private function manageManageForm(Form $form)
    {
        $manageFormData = $form->getData();

        $code = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getRegistrationCodeByCode($manageFormData['manage']['ticket_code']);

        $passengers = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\Passenger')
            ->findPassengerByCode($code);

        $passenger = null;
        if (sizeof($passengers) > 0) {
            $passenger = $passengers[0];
        }

        if (null !== $passenger && $manageFormData['manage']['email'] == $passenger->getEmail()) {
            setcookie(
                self::$cookieNamespace,
                $code->getCode(),
                time() + 3600,
                '/'
            );

            $this->redirect()->toRoute(
                'prom_registration_index',
                array(
                    'action' => 'manage',
                )
            );
        }
    }

    /**
     * @param  Passenger $passenger
     * @param  Bus       $bus
     * @return null
     */
    private function sendConfirmationMail(Passenger $passenger, Bus $bus)
    {
        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.confirmation_mail')
        );

        $mail = new Message();
        $mail->addTo($passenger->getEmail())
            ->setBody(str_replace('{{ busTime }}', $bus->getDepartureTime()->format('d/m/Y H:i'), $mailData['body']))
            ->setFrom($mailData['from'])
            ->addBcc($mailData['from'])
            ->setSubject($mailData['subject']);

        if ('development' != getenv('APPLICATION_ENV')) {
            $this->getMailTransport()->send($mail);
        }
    }

    /**
     * @param  boolean              $checkUsed
     * @return ReservationCode|null
     */
    private function getReservationCodeEntity($checkUsed = true)
    {
        $code = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getRegistrationCodeByCode($this->getCookie());

        if (!($code instanceof ReservationCode)) {
            $this->flashMessenger()->error(
                'Error',
                'No code was found!'
            );

            $this->redirect()->toRoute(
                'prom_registration_index',
                array(
                    'action' => 'registration',
                )
            );

            return;
        }

        if ($code->isUsed() && $checkUsed) {
            $this->flashMessenger()->error(
                'Error',
                'The code you entered was already used to create a bus reservation.'
            );

            $this->redirect()->toRoute(
                'prom_registration_index',
                array(
                    'action' => 'registration',
                )
            );

            return;
        }

        return $code;
    }

    /**
     * @return string
     */
    private function getCookie()
    {
        /** @var \Zend\Http\Header\Cookie $cookies */
        $cookies = $this->getRequest()->getHeader('Cookie');

        return $cookies[self::$cookieNamespace];
    }
}
