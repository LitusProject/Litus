<?php

namespace SecretaryBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use SecretaryBundle\Entity\Pull;

class PullController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Event')
                ->findAllQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    /**
     * To Do: Forms Maken, Repository Maken, Views Maken, Configs, Router
     */
    public function addAction()
    {
        $form = $this->getForm('secretary_admin_pull_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $pull = $form->hydrateObject();

                $this->getEntityManager()->persist($pull);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The event was successfully created!'
                );

                $this->redirect()->toRoute(
                    'secretary_admin_pull',
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

    }

    public function deleteAction()
    {

    }
}
