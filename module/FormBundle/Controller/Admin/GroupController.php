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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Controller\Admin;

use FormBundle\Entity\Node\Form\GroupMap;
use FormBundle\Entity\Node\Group;
use FormBundle\Entity\ViewerMap;
use Laminas\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group')
                ->findAllActive(),
            $this->getParam('page')
        );

        foreach ($paginator as $group) {
            $group->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Group')
                ->findAllOld(),
            $this->getParam('page')
        );

        foreach ($paginator as $group) {
            $group->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('form_group_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $group = $form->hydrateObject(
                    new Group($this->getAuthentication()->getPersonObject())
                );

                $this->getEntityManager()->persist($group);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The group was successfully added!'
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
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
        $group = $this->getGroupEntity();
        if ($group === null) {
            return new ViewModel();
        }

        $group->setEntityManager($this->getEntityManager());

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

        $form = $this->getForm('form_group_edit', $group);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The group was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
                    array(
                        'action' => 'edit',
                        'id'     => $group->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'group' => $group,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $group = $this->getGroupEntity();
        if ($group === null) {
            return new ViewModel();
        }

        if (!$group->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to delete this group!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $this->getEntityManager()->remove($group);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function formsAction()
    {
        $group = $this->getGroupEntity();
        if ($group === null) {
            return new ViewModel();
        }

        $form = $this->getForm('form_form_group-map');

        if ($this->getRequest()->isPost()) {
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

            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $form = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Form')
                    ->findOneById($formData['form']);

                if (count($group->getForms()) > 0) {
                    $form->setStartDate($group->getStartDate())
                        ->setEndDate($group->getEndDate())
                        ->setActive($group->isActive())
                        ->setMax($group->getMax())
                        ->setEditableByUser($group->isEditableByUser())
                        ->setNonMember($group->isNonMember());

                    $formViewers = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\ViewerMap')
                        ->findByForm($form);

                    foreach ($formViewers as $viewer) {
                        $this->getEntityManager()->remove($viewer);
                    }

                    $groupViewers = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\ViewerMap')
                        ->findByForm($group->getForms()[0]->getForm());

                    foreach ($groupViewers as $viewer) {
                        $newViewer = new ViewerMap(
                            $form,
                            $viewer->getPerson(),
                            $viewer->isEdit(),
                            $viewer->isMail()
                        );
                        $this->getEntityManager()->persist($newViewer);
                    }
                }

                if (count($group->getForms()) > 0) {
                    $order = $group->getForms()[count($group->getForms()) - 1]->getOrder() + 1;
                } else {
                    $order = 1;
                }

                $this->getEntityManager()->persist(
                    new GroupMap($form, $group, $order)
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The form was successfully added!'
                );

                $this->redirect()->toRoute(
                    'form_admin_group',
                    array(
                        'action' => 'forms',
                        'id'     => $group->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'group' => $group,
            )
        );
    }

    public function sortAction()
    {
        $this->initAjax();

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

        if (!$this->getRequest()->isPost()) {
            return new ViewModel();
        }

        $data = $this->getRequest()->getPost();

        if (!$data['items']) {
            return new ViewModel();
        }

        foreach ($data['items'] as $order => $id) {
            $mapping = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
                ->findOneById($id);
            $mapping->setOrder($order + 1);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function deleteFormAction()
    {
        $this->initAjax();

        $mapping = $this->getFormGroupMapEntity();
        if ($mapping === null) {
            return new ViewModel();
        }

        if (!$mapping->getGroup()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
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

        $this->getEntityManager()->remove($mapping);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Group|null
     */
    private function getGroupEntity()
    {
        $group = $this->getEntityById('FormBundle\Entity\Node\Group');

        if (!($group instanceof Group)) {
            $this->flashMessenger()->error(
                'Error',
                'No group was found!'
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

    /**
     * @return GroupMap|null
     */
    private function getFormGroupMapEntity()
    {
        $groupMap = $this->getEntityById('FormBundle\Entity\Node\Form\GroupMap');

        if (!($groupMap instanceof GroupMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $groupMap;
    }
}
