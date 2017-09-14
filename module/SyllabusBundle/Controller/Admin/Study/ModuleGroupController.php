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

namespace SyllabusBundle\Controller\Admin\Study;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    SyllabusBundle\Entity\Study\ModuleGroup,
    Zend\View\Model\ViewModel;

/**
 * ModuleGroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ModuleGroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $moduleGroups = $this->search();
        }

        if (!isset($moduleGroups)) {
            $moduleGroups = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                ->findAllQuery();
        }

        $paginator = $this->paginator()->createFromQuery(
            $moduleGroups,
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('syllabus_study_module-group_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $moduleGroup = $form->hydrateObject();

                $this->getEntityManager()->persist($moduleGroup);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The module group was successfully added!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_study_module_group',
                    array(
                        'action' => 'edit',
                        'id' => $moduleGroup->getId(),
                    )
                );
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }

    public function viewAction()
    {
        if (!($moduleGroup = $this->getModuleGroupEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $mappings = $this->searchSubject($moduleGroup, $academicYear);
        }

        if (!isset($mappings)) {
            $mappings = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                ->findAllByModuleGroupAndAcademicYear($moduleGroup, $academicYear);
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'moduleGroup' => $moduleGroup,
                'mappings' => $mappings,
            )
        );
    }

    public function editAction()
    {
        if (!($moduleGroup = $this->getModuleGroupEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $mappings = $this->searchSubject($moduleGroup, $academicYear);
        }

        if (!isset($mappings)) {
            $mappings = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                ->findAllByModuleGroupAndAcademicYear($moduleGroup, $academicYear);
        }

        $form = $this->getForm('syllabus_study_module-group_edit', array('moduleGroup' => $moduleGroup));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The module group was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_study_module_group',
                    array(
                        'action' => 'edit',
                        'id' => $moduleGroup->getId(),
                    )
                );
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'moduleGroup' => $moduleGroup,
                'mappings' => $mappings,
                'form' => $form,
            )
        );
    }

    public function typeaheadAction()
    {
        $moduleGroups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
            ->findAllByTitleQuery($this->getParam('string'))
            ->setMaxResults(20)
            ->getResult();

        $result = array();
        foreach ($moduleGroups as $group) {
            $item = (object) array();
            $item->id = $group->getId();
            $item->value = 'Phase ' . $group->getPhase() . '&mdash;' . $group->getTitle();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $moduleGroups = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($moduleGroups as $moduleGroup) {
            $item = (object) array();
            $item->id = $moduleGroup->getId();
            $item->title = $moduleGroup->getTitle();
            $item->phase = $moduleGroup->getPhase();
            $item->externalId = $moduleGroup->getExternalId();
            $item->mandatory = $moduleGroup->isMandatory() ? 'Yes' : 'No';
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchSubjectAction()
    {
        $this->initAjax();

        if (!($moduleGroup = $this->getModuleGroupEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $subjects = $this->searchSubject($moduleGroup, $academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($subjects as $subject) {
            $item = (object) array();
            $item->id = $subject->getSubject()->getId();
            $item->name = $subject->getSubject()->getName();
            $item->code = $subject->getSubject()->getCode();
            $item->semester = $subject->getSubject()->getSemester();
            $item->credits = $subject->getSubject()->getCredits();
            $item->mandatory = $subject->isMandatory();
            $item->students = $subject->getSubject()->getNbEnrollment($academicYear);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                    ->findAllByTitleQuery($this->getParam('string'));
        }
    }

    /**
     * @param  ModuleGroup              $moduleGroup
     * @param  AcademicYearEntity       $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function searchSubject(ModuleGroup $moduleGroup, AcademicYearEntity $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                    ->findAllByNameAndModuleGroupAndAcademicYearQuery($this->getParam('string'), $moduleGroup, $academicYear);
            case 'code':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                    ->findAllByCodeAndModuleGroupAndAcademicYearQuery($this->getParam('string'), $moduleGroup, $academicYear);
        }
    }

    /**
     * @return ModuleGroup|null
     */
    private function getModuleGroupEntity()
    {
        $moduleGroup = $this->getEntityById('SyllabusBundle\Entity\Study\ModuleGroup');

        if (!($moduleGroup instanceof ModuleGroup)) {
            $this->flashMessenger()->error(
                'Error',
                'No module group was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study_module_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $moduleGroup;
    }

    /**
     * @return AcademicYearEntity|null
     */
    private function getAcademicYearEntity()
    {
        $date = null;
        if (null !== $this->getParam('academicyear')) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (!($academicYear instanceof AcademicYearEntity)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
