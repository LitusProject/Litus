<?php

namespace NotificationBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use NotificationBundle\Entity\Node\Notification;

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
            'NotificationBundle\Entity\Node\Notification',
            $this->getParam('page'),
            array(),
            array('startDate' => 'DESC')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('notification_notification_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $notification = $form->hydrateObject();

                $this->getEntityManager()->persist($notification);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The notification was successfully added!'
                );

                $this->redirect()->toRoute(
                    'notification_admin_notification',
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
        $notification = $this->getNotificationEntity();
        if ($notification === null) {
            return new ViewModel();
        }

        $form = $this->getForm('notification_notification_edit', $notification);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The notification was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'notification_admin_notification',
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

        $notification = $this->getNotificationEntity();
        if ($notification === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($notification);

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
     * @return Notification|null
     */
    private function getNotificationEntity()
    {
        $notification = $this->getEntityById('NotificationBundle\Entity\Node\Notification');

        if (!($notification instanceof Notification)) {
            $this->flashMessenger()->error(
                'Error',
                'No notification was found!'
            );

            $this->redirect()->toRoute(
                'notification_admin_notification',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $notification;
    }
}
