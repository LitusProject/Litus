<?php

namespace ShopBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use ShopBundle\Entity\Session\Message;

/**
 * MessageController
 */
class MessageController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Session\Message')
                ->findAllQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('shop_admin_session_message_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The message was successfully added!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_message',
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
        $message = $this->getMessageEntity();
        if ($message === null) {
            return new ViewModel();
        }

        $form = $this->getForm('shop_admin_session_message_edit', $message);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The message was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_message',
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
                'result' => (object)array('status' => 'success'),
            )
        );
    }

    /**
     * @return Message|null
     */
    private function getMessageEntity()
    {
        $message = $this->getEntityById('ShopBundle\Entity\Session\Message');
        if (!($message instanceof Message)) {
            $this->flashMessenger()->error(
                'Error',
                'No message was found!'
            );

            $this->redirect()->toRoute(
                'shop_admin_shop_message',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $message;
    }
}
