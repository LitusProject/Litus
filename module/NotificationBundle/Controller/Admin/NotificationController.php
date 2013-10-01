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

namespace NotificationBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    NotificationBundle\Entity\Node\Notification,
    NotificationBundle\Entity\Node\Translation,
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
            'NotificationBundle\Entity\Node\Notification',
            $this->getParam('page'),
            array(),
            array('startDate' => 'ASC')
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

            $startDate = DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']);
            $endDate = DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);
                $notification = new Notification(
                    $this->getAuthentication()->getPersonObject(),
                    $startDate ? $startDate : null,
                    $endDate ? $endDate : null,
                    $formData['active']
                );
                $this->getEntityManager()->persist($notification);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if (''!= $formData['content_' . $language->getAbbrev()]) {
                        $notification->addTranslation(
                            new Translation(
                                $notification,
                                $language,
                                str_replace('#', '', $formData['content_' . $language->getAbbrev()])
                            )
                        );
                    }
                }

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

                $startDate = DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']);
                $endDate = DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']);
                if ($endDate)
                    $notification->setEndDate($endDate);
                else
                    $notification->setEndDate(null);

                if ($startDate)
                    $notification->setStartDate($endDate);
                else
                    $notification->setStartDate(null);
                
                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $notification->getTranslation($language, false);

                    if (null !== $translation) {
                        $translation->setContent($formData['content_' . $language->getAbbrev()]);
                    } else {
                        if ('' != $formData['content_' . $language->getAbbrev()]) {
                            $notification->addTranslation(
                                new Translation(
                                    $notification,
                                    $language,
                                    str_replace('#', '', $formData['content_' . $language->getAbbrev()])
                                )
                            );
                        }
                    }
                }

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
            ->getRepository('NotificationBundle\Entity\Node\Notification')
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
