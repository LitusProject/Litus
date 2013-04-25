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

namespace NotificationBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    NotificationBundle\Entity\Nodes\Notification,
    NotificationBundle\Form\Admin\Notification\Add as AddForm,
    NotificationBundle\Form\Admin\Notification\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * NotificationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class NotificationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'NotificationBundle\Entity\Nodes\Notification',
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $notification = new Notification(
                    $this->getAuthentication()->getPersonObject(),
                    $formData['content'],
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['active']
                );
                $this->getEntityManager()->persist($notification);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The notification was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'notification_admin_notification',
                    array(
                        'action' => 'manage'
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
        if (!($notification = $this->_getNotification()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $notification);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $notification->setContent($formData['content'])
                    ->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']))
                    ->setActive($formData['active']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The notification was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'notification_admin_notification',
                    array(
                        'action' => 'manage'
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

        if (!($notification = $this->_getNotification()))
            return new ViewModel();

        $this->getEntityManager()->remove($notification);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getNotification()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the notification!'
                )
            );

            $this->redirect()->toRoute(
                'notification_admin_notification',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $notification = $this->getEntityManager()
            ->getRepository('NotificationBundle\Entity\Nodes\Notification')
            ->findOneById($this->getParam('id'));

        if (null === $notification) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No notification with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'notification_admin_notification',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $notification;
    }
}
