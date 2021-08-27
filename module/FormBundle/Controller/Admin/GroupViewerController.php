<?php

namespace FormBundle\Controller\Admin;

use FormBundle\Entity\Node\Group;
use FormBundle\Entity\ViewerMap;
use Laminas\View\Model\ViewModel;

/**
 * GroupViewerController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class GroupViewerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $group = $this->getGroupEntity();
        if ($group === null) {
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
                'group'   => $group,
                'viewers' => $viewers,
            )
        );
    }

    public function addAction()
    {
        $group = $this->getGroupEntity();
        if ($group === null) {
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
                            'form'   => $group->getForms()[0]->getForm(),
                            'person' => $person,
                        )
                    );

                if ($repositoryCheck !== null) {
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
                        'id'     => $group->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'group' => $group,
                'form'  => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $viewer = $this->getViewerMapEntity();
        if ($viewer === null) {
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
            ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
            ->findOneByForm($viewer->getForm());

        if ($group == null) {
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

    /**
     * @return Group|null
     */
    private function getGroupEntity()
    {
        $group = $this->getEntityById('FormBundle\Entity\Node\Group');

        if (!($group instanceof Group) || count($group->getForms()) == 0) {
            $this->flashMessenger()->error(
                'Error',
                'No group was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $group;
    }

    /**
     * @return ViewerMap|null
     */
    private function getViewerMapEntity()
    {
        $viewer = $this->getEntityById('FormBundle\Entity\ViewerMap');

        if (!($viewer instanceof ViewerMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No viewer was found!'
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
