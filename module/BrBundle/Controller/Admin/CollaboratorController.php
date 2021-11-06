<?php

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Collaborator;
use Laminas\View\Model\ViewModel;

/**
 * CollaboratorController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class CollaboratorController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Collaborator',
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
        $form = $this->getForm('br_collaborator_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The collaborator was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_collaborator',
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
        $collaborator = $this->getCollaboratorEntity();
        if ($collaborator === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_collaborator_edit', $collaborator);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The collaborator was succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_collaborator',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'         => $form,
                'collaborator' => $collaborator,
            )
        );
    }

    public function retireAction()
    {
        $collaborator = $this->getCollaboratorEntity();
        if ($collaborator === null) {
            return new ViewModel();
        }

        $collaborator->retire();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The collaborator succesfully retired!'
        );

        $this->redirect()->toRoute(
            'br_admin_collaborator',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function rehireAction()
    {
        $collaborator = $this->getCollaboratorEntity();
        if ($collaborator === null) {
            return new ViewModel();
        }

        $collaborator->rehire();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The collaborator succesfully rehired!'
        );

        $this->redirect()->toRoute(
            'br_admin_collaborator',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    /**
     * @return Collaborator|null
     */
    private function getCollaboratorEntity()
    {
        $collaborator = $this->getEntityById('BrBundle\Entity\Collaborator');

        if (!($collaborator instanceof Collaborator)) {
            $this->flashMessenger()->error(
                'Error',
                'No collaborator was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $collaborator;
    }
}
