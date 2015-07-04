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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Entity\Acl\Action,
    CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\User\Person,
    Zend\View\Model\ViewModel;

/**
 * RoleController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RoleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CommonBundle\Entity\Acl\Role',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('common_role_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->updateCache();

                $this->flashMessenger()->success(
                    'Success',
                    'The role was successfully created!'
                );

                $this->redirect()->toRoute(
                    'common_admin_role',
                    array(
                        'action' => 'add',
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

    public function membersAction()
    {
        if (!($role = $this->getRoleEntity())) {
            return new ViewModel();
        }

        $members = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findAllByRole($role);

        return new ViewModel(
            array(
                'role' => $role,
                'members' => $members,
            )
        );
    }

    public function editAction()
    {
        if (!($role = $this->getRoleEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('common_role_edit', array('role' => $role));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->updateCache();

                $this->flashMessenger()->success(
                    'Success',
                    'The role was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'common_admin_role',
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

        if (!($role = $this->getRoleEntity())) {
            return new ViewModel();
        }

        $users = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findAllByRole($role);

        foreach ($users as $user) {
            $user->removeRole($role);
        }
        $this->getEntityManager()->remove($role);

        $this->getEntityManager()->flush();

        $this->updateCache();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteMemberAction()
    {
        $this->initAjax();

        if (!($role = $this->getRoleEntity())) {
            return new ViewModel();
        }

        if (!($member = $this->getPersonEntity())) {
            return new ViewModel();
        }

        $member->removeRole($role);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function pruneAction()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        foreach ($roles as $role) {
            foreach ($role->getActions() as $action) {
                if ($this->findActionWithParents($action, $role->getParents())) {
                    $role->removeAction($action);
                }
            }
        }

        $this->getEntityManager()->flush();

        $this->updateCache();

        $this->flashMessenger()->success(
            'Success',
            'The tree was successfully pruned!'
        );

        $this->redirect()->toRoute(
            'common_admin_role',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    /**
     * @return Role|null
     */
    private function getRoleEntity()
    {
        $role = $this->getEntityById('CommonBundle\Entity\Acl\Role', 'name', 'name');

        if (!($role instanceof Role)) {
            $this->flashMessenger()->error(
                'Error',
                'No role was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_role',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $role;
    }

    /**
     * @return Person|null
     */
    private function getPersonEntity()
    {
        $person = $this->getEntityById('CommonBundle\Entity\User\Person');

        if (!($person instanceof Person)) {
            $this->flashMessenger()->error(
                'Error',
                'No person was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_role',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $person;
    }

    /**
     * @return null
     */
    private function updateCache()
    {
        if (null !== $this->getCache() && $this->getCache()->hasItem('CommonBundle_Component_Acl_Acl')) {
            $this->getCache()->replaceItem(
                'CommonBundle_Component_Acl_Acl',
                new Acl(
                    $this->getEntityManager()
                )
            );
        }
    }

    /**
     * @param  Action  $action
     * @param  array   $parents
     * @return boolean
     */
    private function findActionWithParents(Action $action, array $parents)
    {
        foreach ($parents as $parent) {
            if (in_array($action, $parent->getActions())) {
                return true;
            }

            if ($this->findActionWithParents($action, $parent->getParents())) {
                return true;
            }
        }

        return false;
    }
}
