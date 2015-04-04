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

namespace BrBundle\Controller\Admin;

use Zend\View\Model\ViewModel;

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
                'paginator' => $paginator,
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
        if (!($collaborator = $this->getCollaborator())) {
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
                'form' => $form,
                'collaborator' => $collaborator,
            )
        );
    }

    public function retireAction()
    {
        if (!($collaborator = $this->getCollaborator())) {
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
        if (!($collaborator = $this->getCollaborator())) {
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

    private function getCollaborator()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the collaborator!'
            );

            $this->redirect()->toRoute(
                'br_admin_collaborator',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $collaborator = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Collaborator')
            ->findOneById($this->getParam('id'));

        if (null === $collaborator) {
            $this->flashMessenger()->error(
                'Error',
                'No collaborator with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_collaborator',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $collaborator;
    }
}
