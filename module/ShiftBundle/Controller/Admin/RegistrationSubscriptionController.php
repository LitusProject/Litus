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

use ShiftBundle\Entity\RegistrationShift;
use ShiftBundle\Entity\Shift\Registered;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use function GuzzleHttp\Psr7\str;

/**
 * RegistrationSubscriptionController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RegistrationSubscriptionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $shift = $this->getRegistrationShiftEntity();
        if ($shift === null) {
            return new ViewModel();
        }

        $registered = $shift->getRegistered();

        $form = $this->getForm('shift_registrationSubscription_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $subscriber = $form->hydrateObject($shift);

                if ($subscriber === null) {
                    $this->flashMessenger()->error(
                        'Error',
                        'Unable to add the given person to the registration shift!'
                    );

                    $this->redirect()->toRoute(
                        'shift_admin_registration_shift_subscription',
                        array(
                            'action' => 'manage',
                            'id'     => $shift->getId(),
                        )
                    );

                    return new ViewModel();
                }

                $this->getEntityManager()->persist($subscriber);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'shift_admin_registration_shift_subscription',
                    array(
                        'action' => 'manage',
                        'id'     => $shift->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'         => $form,
                'shift'        => $shift,
                'registered'   => $registered,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();
        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }
        $repository = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\RegistrationShift');
        $shift = $repository->findOneByRegistered($subscription->getId());
        $shift->removeRegistered($subscription->getPerson());


        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail_name');

        $language = $subscription->getPerson()->getLanguage();
        if ($language === null) {
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

        if (getenv('APPLICATION_ENV') != 'development') {
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
     * @return Registered|null
     */
    private function getSubscriptionEntity()
    {
        $subscription = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift\Registered')
            ->findOneById($this->getParam('id', 0));

        if ($subscription === null) {
            $this->flashMessenger()->error(
                'Error',
                'No subscription with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_registration_shift',
                array(
                    'action' => 'manage',
                    'shift' => $this->getEntityById('ShiftBundle\Entity\RegistrationShift', 'shift'),
                )
            );

            return;
        }

        return $subscription;
    }

    /**
     * @return RegistrationShift|null
     */
    private function getRegistrationShiftEntity()
    {
        $shift = $this->getEntityById('ShiftBundle\Entity\RegistrationShift', 'shift');

        if (!($shift instanceof RegistrationShift)) {
            $this->flashMessenger()->error(
                'Error',
                'No registration shift was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_registration_shift',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $shift;
    }
}
