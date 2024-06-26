<?php

namespace SyllabusBundle\Controller\Admin\Study;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use Laminas\View\Model\ViewModel;
use SyllabusBundle\Entity\Study\ModuleGroup;

/**
 * ModuleGroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ModuleGroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
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
                        'id'     => $moduleGroup->getId(),
                    )
                );
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
            )
        );
    }

    public function viewAction()
    {
        $moduleGroup = $this->getModuleGroupEntity();
        if ($moduleGroup === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'moduleGroup'         => $moduleGroup,
                'mappings'            => $mappings,
            )
        );
    }

    public function editAction()
    {
        $moduleGroup = $this->getModuleGroupEntity();
        if ($moduleGroup === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
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
                        'id'     => $moduleGroup->getId(),
                    )
                );
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'moduleGroup'         => $moduleGroup,
                'mappings'            => $mappings,
                'form'                => $form,
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

        $moduleGroup = $this->getModuleGroupEntity();
        if ($moduleGroup === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
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
     * @param  ModuleGroup        $moduleGroup
     * @param  AcademicYearEntity $academicYear
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
        if ($this->getParam('academicyear') !== null) {
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
