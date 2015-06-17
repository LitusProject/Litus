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

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\Acl\Role,
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
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $units = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Organization\Unit')->findAll();

        $unitsWithMembers = array();
        foreach ($units as $key => $unit) {
            $members = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
            ->findBy(array('unit' => $unit, 'academicYear' => $academicYear));
            if (isset($members[0])) {
                array_push($unitsWithMembers, $unit);
                unset($units[$key]);
            }
        }

        return new ViewModel(
            array(
                'unitsWithMembers' => $unitsWithMembers,
                'emptyUnits' => $units,
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
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

    public function membersAction()
    {
        if (!($unit = $this->getUnitEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $form = $this->getForm('common_unit_member');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person']['id']);

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                    ->findOneBy(
                        array(
                            'unit' => $unit,
                            'academic' => $academic,
                            'academicYear' => $academicYear,
                        )
                    );

                if (null !== $repositoryCheck) {
                    $this->flashMessenger()->error(
                        'Error',
                        'This academic already is a member of this unit!'
                    );
                } else {
                    $member = new UnitMap($academic, $academicYear, $unit, $formData['coordinator']);

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
            ->findBy(array('unit' => $unit, 'academicYear' => $academicYear));

        return new ViewModel(
            array(
                'unit' => $unit,
                'form' => $form,
                'members' => $members,
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
            )
        );
    }

    public function editAction()
    {
        if (!($unit = $this->getUnitEntity())) {
            return new ViewModel();
        }

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

        if (!($unit = $this->getUnitEntity())) {
            return new ViewModel();
        }

        $unit->deactivate();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function deleteMemberAction()
    {
        $this->initAjax();

        if (!($unitMap = $this->getUnitMapEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($unitMap);
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
                if ($this->findRoleWithParent($role, $unit->getParent())) {
                    $unit->removeRole($role);
                }
            }

            foreach ($unit->getCoordinatorRoles() as $coordinatorRole) {
                if ($this->findCoordinatorRoleWithParent($coordinatorRole, $unit->getParent())) {
                    $unit->removeCoordinatorRole($coordinatorRole);
                }
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
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    /**
     * @return Unit|null
     */
    private function getUnitEntity()
    {
        $unit = $this->getEntityById('CommonBundle\Entity\General\Organization\Unit');

        if (!($unit instanceof Unit)) {
            $this->flashMessenger()->error(
                'Error',
                'No unit was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $unit;
    }

    /**
     * @return UnitMap|null
     */
    private function getUnitMapEntity()
    {
        $unitMap = $this->getEntityById('CommonBundle\Entity\User\Person\Organization\UnitMap');

        if (!($unitMap instanceof UnitMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No unit map was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $unitMap;
    }

    /**
     * @param  Role      $role
     * @param  Unit|null $parent
     * @return boolean
     */
    private function findRoleWithParent(Role $role, Unit $parent = null)
    {
        if (null === $parent) {
            return false;
        }

        if (in_array($role, $parent->getRoles(false))) {
            return true;
        }

        return $this->findRoleWithParent($role, $parent->getParent());
    }

    /**
     * @param  Role      $role
     * @param  Unit|null $parent
     * @return boolean
     */
    private function findCoordinatorRoleWithParent(Role $role, Unit $parent = null)
    {
        if (null === $parent) {
            return false;
        }

        if (in_array($role, $parent->getCoordinatorRoles(false))) {
            return true;
        }

        return $this->findCoordinatorRoleWithParent($role, $parent->getParent());
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYearEntity()
    {
        if (null === $this->getParam('academicyear')) {
            return $this->getCurrentAcademicYear();
        }

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_registration',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
