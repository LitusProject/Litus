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

use CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\General\Organization\Unit,
    CommonBundle\Entity\User\Person\Organization\UnitMap,
    Zend\View\Model\ViewModel;

/**
 * UnitController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class UnitController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CommonBundle\Entity\General\Organization\Unit',
            $this->getParam('page'),
            array(
                'active' => true
            ),
            array(
                'name' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('common_unit_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The unit was successfully created!'
                );

                $this->redirect()->toRoute(
                    'common_admin_unit',
                    array(
                        'action' => 'manage'
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
        if(!($unit = $this->_getUnit()))

            return new ViewModel();

        $form = $this->getForm('common_unit_member');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['person_id']);
                }

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                    ->findOneBy(
                        array(
                            'unit' => $unit,
                            'academic' => $academic,
                            'academicYear' => $this->getCurrentAcademicYear()
                        )
                    );

                if (null !== $repositoryCheck) {
                    $this->flashMessenger()->error(
                        'Error',
                        'This academic already is a member of this unit!'
                    );
                } else {
                    $member = new UnitMap($academic, $this->getCurrentAcademicYear(), $unit, $formData['coordinator']);

                    $this->getEntityManager()->persist($member);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The member was succesfully added!'
                    );
                }

                $this->redirect()->toRoute(
                    'common_admin_unit',
                    array(
                        'action' => 'members',
                        'id' => $unit->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $members = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
            ->findBy(array('unit' => $unit, 'academicYear' => $this->getCurrentAcademicYear()));

        return new ViewModel(
            array(
                'unit' => $unit,
                'form' => $form,
                'members' => $members,
            )
        );
    }

    public function editAction()
    {
        if (!($unit = $this->_getUnit()))
            return new ViewModel();

        $form = $this->getForm('common_unit_edit', array('unit' => $unit));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The unit was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'common_admin_unit',
                    array(
                        'action' => 'manage'
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

        if (!($unit = $this->_getUnit()))
            return new ViewModel();

        $unit->deactivate();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function deleteMemberAction()
    {
        $this->initAjax();

        if (!($member = $this->_getMember()))
            return new ViewModel();

        $this->getEntityManager()->remove($member);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function pruneAction()
    {
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAll();

        foreach ($units as $unit) {
            foreach ($unit->getRoles() as $role) {
                if ($this->_findRoleWithParent($role, $unit->getParent()))
                    $unit->removeRole($role);
            }

            foreach ($unit->getCoordinatorRoles() as $coordinatorRole) {
                if ($this->_findCoordinatorRoleWithParent($coordinatorRole, $unit->getParent()))
                    $unit->removeCoordinatorRole($coordinatorRole);
            }
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Succes',
            'The tree was succesfully pruned!'
        );

        $this->redirect()->toRoute(
            'common_admin_unit',
            array(
                'action' => 'manage'
            )
        );

        return new ViewModel();
    }

    /**
     * @return Unit|null
     */
    private function _getUnit()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the unit!'
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $unit = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findOneById($this->getParam('id'));

        if (null === $unit) {
            $this->flashMessenger()->error(
                'Error',
                'No unit with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $unit;
    }

    /**
     * @return UnitMap|null
     */
    private function _getMember()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the member!'
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $member = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
            ->findOneById($this->getParam('id'));

        if (null === $member) {
            $this->flashMessenger()->error(
                'Error',
                'No member with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $member;
    }

    /**
     * @param Role      $role
     * @param Unit|null $parent
     */
    private function _findRoleWithParent(Role $role, Unit $parent = null)
    {
        if (null === $parent)
            return false;

        if (in_array($role, $parent->getRoles(false)))
            return true;

        return $this->_findRoleWithParent($role, $parent->getParent());
    }

    /**
     * @param Role      $role
     * @param Unit|null $parent
     */
    private function _findCoordinatorRoleWithParent(Role $role, Unit $parent = null)
    {
        if (null === $parent)
            return false;

        if (in_array($role, $parent->getCoordinatorRoles(false)))
            return true;

        return $this->_findCoordinatorRoleWithParent($role, $parent->getParent());
    }
}
