<?php

namespace MailBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use MailBundle\Entity\Message;

/**
 * MessageController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MessageController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'MailBundle\Entity\Message',
            $this->getParam('page'),
            array(),
            array(
                'creationTime' => 'DESC',
            )
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function editAction()
    {
        $message = $this->getMessageEntity();
        if ($message === null) {
            return new ViewModel();
        }

        $form = $this->getForm('mail_message_edit', $message);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The message was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_message',
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

        $message = $this->getMessageEntity();
        if ($message === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($message);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Message|null
     */
    private function getMessageEntity()
    {
        $message = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Message')
            ->findOneById($this->getParam('id', 0));

        if (!($message instanceof Message)) {
            $this->flashMessenger()->error(
                'Error',
                'No message was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_message',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $message;
    }
}
