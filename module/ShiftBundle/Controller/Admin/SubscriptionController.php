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

namespace ShiftBundle\Controller\Admin;

use ShiftBundle\Entity\Shift,
    ShiftBundle\Entity\Shift\Responsible,
    ShiftBundle\Entity\Shift\Volunteer,
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
        if (!($shift = $this->getShiftEntity())) {
            return new ViewModel();
        }

        $responsibles = $shift->getResponsibles();
        $volunteers = $shift->getVolunteers();

        $form = $this->getForm('shift_subscription_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $subscriber = $form->hydrateObject($shift);

                if (null === $subscriber) {
                    $this->flashMessenger()->error(
                        'Error',
                        'Unable to add the given person to the shift!'
                    );

                    $this->redirect()->toRoute(
                        'shift_admin_shift_subscription',
                        array(
                            'action' => 'manage',
                            'id' => $shift->getId(),
                        )
                    );

                    return new ViewModel();
                }

                $this->getEntityManager()->persist($subscriber);
                $this->getEntityManager()->flush();

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

        if (!($subscription = $this->getSubscriptionEntity())) {
            return new ViewModel();
        }

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
        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ shift }}', $shiftString, $message))
            ->setFrom($mailAddress, $mailName)
            ->addTo($subscription->getPerson()->getEmail(), $subscription->getPerson()->getFullName())
            ->setSubject($subject);

        if ('development' != getenv('APPLICATION_ENV')) {
            $this->getMailTransport()->send($mail);
        }

        $this->getEntityManager()->remove($subscription);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return mixed
     */
    private function getSubscriptionEntity()
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
                $this->flashMessenger()->error(
                    'Error',
                    'The given type is not valid!'
                );

                $this->redirect()->toRoute(
                    'shift_admin_shift',
                    array(
                        'action' => 'manage',
                    )
                );

                return;
        }

        $subscription = $repository->findOneById($this->getParam('id', 0));

        if (null === $subscription) {
            $this->flashMessenger()->error(
                'Error',
                'No subscription with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $subscription;
    }

    /**
     * @return Shift|null
     */
    private function getShiftEntity()
    {
        $shift = $this->getEntityById('ShiftBundle\Entity\Shift');

        if (!($shift instanceof Shift)) {
            $this->flashMessenger()->error(
                'Error',
                'No shift was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $shift;
    }
}
