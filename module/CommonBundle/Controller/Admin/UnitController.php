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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\Acl\Role;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person\Organization\UnitMap\Academic as UnitMapAcademic;
use CommonBundle\Entity\User\Person\Organization\UnitMap\External as UnitMapExternal;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Laminas\Http\Headers;
use Imagick;
use Laminas\View\Model\ViewModel;

/**
 * UnitController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class UnitController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAll();

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
                'unitsWithMembers'   => $unitsWithMembers,
                'emptyUnits'         => $units,
                'activeAcademicYear' => $academicYear,
                'academicYears'      => $academicYears,
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

    // Generate CSV from units for use in email lists
    public function csvAction()
    {
        $file = new CsvFile();
        $heading = array('domain','mailacceptinggeneralid','maildrop');

        $domain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.mail_domain');

        $academicYear = $this->getAcademicYearEntity();

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAll();

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

        $results = array();
        foreach ($unitsWithMembers as $unit) {
            $unitFormat = strtolower(str_replace(' ','',$unit->getName()));
            $unitYearFormat = $unitFormat . '_' . $academicYear->getCode(true);

            $members = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                ->findBy(array('unit' => $unit, 'academicYear' => $academicYear));

            foreach ($members as $member) {
                $lastNameFormat = str_replace(' ', '', $member->getLastName());
                $nameFormatWithAccents = strtolower($member->getFirstName() . '.' . $lastNameFormat);
                // Remove accents by transliterating one character set onto another
                // setlocale is necessary, otherwise accents become question marks
                setlocale(LC_ALL, 'en_US.utf8');
                $nameFormat = iconv("UTF-8",'ASCII//TRANSLIT',$nameFormatWithAccents);

                $results[] = array($domain, $unitYearFormat, $nameFormat);
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="mailinglistdata.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function membersAction()
    {
        $unit = $this->getUnitEntity();
        if ($unit === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicForm = $this->getForm('common_unit_academic');
        $externalForm = $this->getForm('common_unit_external');

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $academicForm->setData($formData);
            $externalForm->setData(
                array_merge_recursive(
                    $formData->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($formData['mapType'] == 'academic' && $academicForm->isValid()) {
                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person']['id']);

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap\Academic')
                    ->findOneBy(
                        array(
                            'unit'         => $unit,
                            'academic'     => $academic,
                            'academicYear' => $academicYear,
                        )
                    );

                if ($repositoryCheck !== null) {
                    $this->flashMessenger()->error(
                        'Error',
                        'This academic already is a member of this unit!'
                    );
                } else {
                    $member = new UnitMapAcademic($academic, $academicYear, $unit, $formData['coordinator'], $formData['description']);

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
                        'action'       => 'members',
                        'id'           => $unit->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            } elseif ($formData['mapType'] == 'external' && $externalForm->isValid()) {
                $formData = $externalForm->getData();

                $image = new Imagick($formData['picture']['tmp_name']);
                $image->thumbnailImage(180, 135, true);

                do {
                    $fileName = sha1(uniqid());
                } while (file_exists($filePath . $fileName));

                $image->writeImage('public/' . $filePath . '/' . $fileName);

                $member = new UnitMapExternal($formData['first_name'], $formData['last_name'], '/' . $fileName, $academicYear, $unit, $formData['coordinator'], $formData['description']);

                $this->getEntityManager()->persist($member);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The member was succesfully added!'
                );

                $this->redirect()->toRoute(
                    'common_admin_unit',
                    array(
                        'action'       => 'members',
                        'id'           => $unit->getId(),
                        'academicyear' => $academicYear->getCode(),
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
                'unit'               => $unit,
                'academicForm'       => $academicForm,
                'externalForm'       => $externalForm,
                'members'            => $members,
                'activeAcademicYear' => $academicYear,
                'academicYears'      => $academicYears,
            )
        );
    }

    public function editAction()
    {
        $unit = $this->getUnitEntity();
        if ($unit === null) {
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

        $unit = $this->getUnitEntity();
        if ($unit === null) {
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

        $unitMap = $this->getUnitMapEntity();
        if ($unitMap === null) {
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
     * @return \CommonBundle\Entity\User\Person\Organization\UnitMap|null
     */
    private function getUnitMapEntity()
    {
        $unitMap = $this->getEntityById('CommonBundle\Entity\User\Person\Organization\UnitMap');

        if (!($unitMap instanceof UnitMapAcademic || $unitMap instanceof UnitMapExternal)) {
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
        if ($parent === null) {
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
        if ($parent === null) {
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
        if ($this->getParam('academicyear') === null) {
            return $this->getCurrentAcademicYear();
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
}
