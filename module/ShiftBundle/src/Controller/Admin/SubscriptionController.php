<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    ShiftBundle\Entity\Shift,
    ShiftBundle\Form\Admin\Shift\Add as AddForm,
    ShiftBundle\Form\Admin\Shift\Edit as EditForm,
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

        return new ViewModel(
            array(
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

        // @TODO: Send an e-mail to this guy
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

        switch($type) {
            case 'volunteer':
                $repository = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shifts\Volunteer');
                break;
            case 'responsible':
                $repository = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shifts\Responsible');
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
                    'admin_shift',
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
                'admin_shift',
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
                'admin_shift',
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
                'admin_shift',
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
                'admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $shift;
    }
}
