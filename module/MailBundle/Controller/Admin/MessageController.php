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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Form\Admin\Message\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * MessageController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MessageController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromDocument(
            'MailBundle\Document\Message',
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function editAction()
    {
        if (!($message = $this->_getMessage()))
            return new ViewModel();

        $form = new EditForm($message);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                var_dump("Hello");

                $formData = $form->getFormData($formData);

                $message->setSubject($formData['subject'])
                    ->setBody($formData['body']);

                $this->getDocumentManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The message was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'mail_admin_message',
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

        if (!($message = $this->_getMessage()))
            return new ViewModel();

        $this->getDocumentManager()->remove($message);

        $this->getDocumentManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getMessage()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the message!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_message',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $message = $this->getDocumentManager()
            ->getRepository('MailBundle\Document\Message')
            ->findOneById($this->getParam('id'));

        if (null === $message) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No message with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_message',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $message;
    }
}
