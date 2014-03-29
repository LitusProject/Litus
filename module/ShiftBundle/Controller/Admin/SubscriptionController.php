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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    ShiftBundle\Entity\Shift,
    ShiftBundle\Entity\Shift\Responsible,
    ShiftBundle\Entity\Shift\Volunteer,
    ShiftBundle\Form\Admin\Subscription\Add as AddForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SubscriptionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($shift = $this->_getShift()))
            return new ViewModel();

        $responsibles = $shift->getResponsibles();
        $volunteers = $shift->getVolunteers();

        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {

                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');
                if ($formData['person_id'] == '') {
                    $person = $repository->findOneByUsername($formData['person_name']);
                } else {
                    $person = $repository->findOneById($formData['person_id']);
                }

                if ($formData['responsible']) {
                    if (!$shift->canHaveAsResponsible($this->getEntityManager(), $person)) {
                        $this->_invalidAdd($shift);

                        return new ViewModel();
                    }

                    $responsible = new Responsible($person, $this->getCurrentAcademicYear());
                    $shift->addResponsible($this->getEntityManager(), $responsible);
                    $this->getEntityManager()->persist($responsible);
                    $this->getEntityManager()->flush();
                } else {
                    if (!$shift->canHaveAsVolunteer($this->getEntityManager(), $person)) {
                        $this->_invalidAdd($shift);

                        return new ViewModel();
                    }

                    $volunteer = new Volunteer($person, $this->getCurrentAcademicYear());
                    $shift->addVolunteer($this->getEntityManager(), $volunteer);
                    $this->getEntityManager()->persist($volunteer);
                    $this->getEntityManager()->flush();
                }

                $this->redirect()->toRoute(
                    'shift_admin_shift_subscription',
                    array(
                        'action' => 'manage',
                        'id' => $shift->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'shift' => $shift,
                'volunteers' => $volunteers,
                'responsibles' => $responsibles,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($subscription = $this->_getSubscription()))
            return new ViewModel();

        $repository = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift');
        switch ($this->getParam('type')) {
            case 'volunteer':
                $shift = $repository->findOneByVolunteer($subscription->getId());
                $shift->removeVolunteer($subscription);
                break;
            case 'responsible':
                $shift = $repository->findOneByResponsible($subscription->getId());
                $shift->removeResponsible($subscription);
                break;
            default:
                return new ViewModel();
        }

        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail_name');

        if (!($language = $subscription->getPerson()->getLanguage())) {
            $language = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.subscription_deleted_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

        $mail = new Message();
        $mail->setBody(str_replace('{{ shift }}', $shiftString, $message))
            ->setFrom($mailAddress, $mailName)
            ->addTo($subscription->getPerson()->getEmail(), $subscription->getPerson()->getFullName())
            ->setSubject($subject);

        if ('development' != getenv('APPLICATION_ENV'))
            $this->getMailTransport()->send($mail);

        $this->getEntityManager()->remove($subscription);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getSubscription()
    {
        $type = $this->getParam('type');

        switch ($type) {
            case 'volunteer':
                $repository = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift\Volunteer');
                break;
            case 'responsible':
                $repository = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift\Responsible');
                break;
            default:
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::ERROR,
                        'Error',
                        'The given type is not valid!'
                    )
                );

                $this->redirect()->toRoute(
                    'shift_admin_shift',
                    array(
                        'action' => 'manage'
                    )
                );

                return;
        }

        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the subscription!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $subscription = $repository->findOneById($this->getParam('id'));

        if (null === $subscription) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No subscription with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $subscription;
    }

    private function _getShift()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the shift!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getParam('id'));

        if (null === $shift) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No shift with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $shift;
    }

    private function _invalidAdd(Shift $shift)
    {
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::ERROR,
                'Error',
                'Unable to add the given person to the shift!'
            )
        );

        $this->redirect()->toRoute(
            'shift_admin_shift_subscription',
            array(
                'action' => 'manage',
                'id' => $shift->getId(),
            )
        );
    }
}
