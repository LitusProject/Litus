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

namespace FormBundle\Controller\Admin;

use FormBundle\Entity\ViewerMap,
    Zend\View\Model\ViewModel;

/**
 * GroupViewerController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class GroupViewerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($group = $this->getGroup())) {
            return new ViewModel();
        }

        if (!$group->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this group!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findByForm($group->getForms()[0]->getForm());

        return new ViewModel(
            array(
                'group' => $group,
                'viewers' => $viewers,
            )
        );
    }

    public function addAction()
    {
        if (!($group = $this->getGroup())) {
            return new ViewModel();
        }

        if (!$group->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this group!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $form = $this->getForm('form_viewer_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person')
                    ->findOneById($formData['person']['id']);

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\ViewerMap')
                    ->findOneBy(
                        array(
                            'form' => $group->getForms()[0]->getForm(),
                            'person' => $person,
                        )
                    );

                if (null !== $repositoryCheck) {
                    $this->flashMessenger()->error(
                        'Error',
                        'This user has already been given access to this list!'
                    );
                } else {
                    foreach ($group->getForms() as $formMapping) {
                        $this->getEntityManager()->persist(
                            $form->hydrateObject(new ViewerMap($formMapping->getForm()))
                        );
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The viewer was successfully created!'
                    );
                }

                $this->redirect()->toRoute(
                    'form_admin_group_viewer',
                    array(
                        'action' => 'manage',
                        'id' => $group->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'group' => $group,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($viewer = $this->getViewer())) {
            return new ViewModel();
        }

        if (!$viewer->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this group!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($viewer->getForm());

        if (null == $group) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllByGroupAndPerson($group->getGroup(), $viewer->getPerson());

        foreach ($viewers as $viewer) {
            $this->getEntityManager()->remove($viewer);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function getGroup()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the group!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            $this->flashMessenger()->error(
                'Error',
                'No group with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if (sizeof($group->getForms()) == 0) {
            $this->flashMessenger()->error(
                'Error',
                'This group has no forms!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $group;
    }

    private function getViewer()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the viewer!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $viewer = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneById($this->getParam('id'));

        if (null === $viewer) {
            $this->flashMessenger()->error(
                'Error',
                'No viewer with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $viewer;
    }
}
