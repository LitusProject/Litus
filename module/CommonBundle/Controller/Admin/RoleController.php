<?php

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Acl\Acl;
use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\Acl\Action;
use CommonBundle\Entity\Acl\Role;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use CommonBundle\Entity\User\Person;
use Laminas\View\Model\ViewModel;

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
                'paginator'         => $paginator,
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
        $role = $this->getRoleEntity();
        if ($role === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $members = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findAllByRole($role);

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAll();
        $unitMembers = array();
        foreach ($units as $unit) {
            $unitRoles = $unit->getRoles();
            foreach ($unitRoles as $unitRole) {
                if ($unitRole == $role) {
                    $unitMembers = array_unique(
                        array_merge(
                            $unitMembers,
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                                ->findBy(array('unit' => $unit, 'academicYear' => $academicYear))
                        ),
                        SORT_REGULAR
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'role'        => $role,
                'members'     => $members,
                'unitMembers' => $unitMembers,
            )
        );
    }

    public function editAction()
    {
        $role = $this->getRoleEntity();
        if ($role === null) {
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

        $role = $this->getRoleEntity();
        if ($role === null) {
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

        $role = $this->getRoleEntity();
        if ($role === null) {
            return new ViewModel();
        }

        $member = $this->getPersonEntity();
        if ($member === null) {
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
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYearEntity()
    {
        if ($this->getParam('academicyear') === null) {
            return $this->getCurrentAcademicYear(true);
        }

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (!($academicYear instanceof AcademicYearEntity)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * @return null
     */
    private function updateCache()
    {
        if ($this->getCache() !== null && $this->getCache()->hasItem('CommonBundle_Component_Acl_Acl')) {
            $this->getCache()->replaceItem(
                'CommonBundle_Component_Acl_Acl',
                new Acl(
                    $this->getEntityManager()
                )
            );
        }
    }

    /**
     * @param  Action $action
     * @param  array  $parents
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
