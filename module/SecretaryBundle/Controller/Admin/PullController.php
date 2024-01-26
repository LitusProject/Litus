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
                ->getRepository('SecretaryBundle\Entity\Pull')
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
        $pull = $this->getPullEntity();

        if ($pull === null) {
            return new ViewModel();
        }

        $form = $this->getForm('secretary_admin_pull_edit', array('pull' => $pull));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
//                die(json_encode($form->getData()));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The pull was succesfully updated!'
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

    public function deleteAction()
    {
        $this->initAjax();

        $pull = $this->getPullEntity();
        if ($pull === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($pull);
        $this->getEntityManager()->flush();

//        $this->redirect()->toRoute(
//            'secretary_admin_pull',
//            array(
//                'action_manage',
//            )
//        );

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function getPullEntity()
    {
        $pull = $this->getEntityById('SecretaryBundle\Entity\Pull');

        if (!($pull instanceof Pull)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_pull',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }
        return $pull;
    }
}
