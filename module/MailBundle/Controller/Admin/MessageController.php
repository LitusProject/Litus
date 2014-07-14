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

namespace MailBundle\Controller\Admin;

use MailBundle\Form\Admin\Message\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * MessageController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MessageController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromDocument(
            'MailBundle\Document\Message',
            $this->getParam('page'),
            array(),
            array(
                'creationTime' => 'DESC'
            )
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

        $form = $this->getForm('mail_message_edit', $message);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getDocumentManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The message was successfully edited!'
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
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \MailBundle\Document\Message|null
     */
    private function _getMessage()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the message!'
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
            $this->flashMessenger()->error(
                'Error',
                'No message with the given ID was found!'
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
