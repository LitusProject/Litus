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

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    SyllabusBundle\Form\Admin\Study\Add as AddForm,
    SyllabusBundle\Form\Admin\Study\Edit as EditForm,
    SyllabusBundle\Entity\AcademicYearMap,
    SyllabusBundle\Entity\Study,
    Zend\View\Model\ViewModel;

/**
 * StudyController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StudyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (null !== $this->getParam('field'))
            $mappings = $this->_search($academicYear);

        if (!isset($mappings)) {
            $mappings = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\AcademicYearMap')
                ->findAllByAcademicYear($academicYear);
        }

        $paginator = $this->paginator()->createFromArray(
            $mappings,
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
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $parent = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['parent_id']);

                $study = new Study($formData['title'], $formData['kul_id'], $formData['phase'], $formData['language'], $parent);
                $this->getEntityManager()->persist($study);

                $map = new AcademicYearMap($study, $academicYear);
                $this->getEntityManager()->persist($map);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The study was successfully added!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_study',
                    array(
                        'action' => 'edit',
                        'id' => $study->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function editAction()
    {
        if (!($study = $this->_getStudy()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (null !== $this->getParam('field'))
            $mappings = $this->_searchSubject($study, $academicYear);

        if (!isset($mappings)) {
            $mappings = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                ->findAllByStudyAndAcademicYear($study, $academicYear);
        }

        $form = new EditForm($this->getEntityManager(), $study);

        if ($this->getRequest()->isPost() && $this->hasAccess()->toResourceAction('syllabus_admin_study', 'add')) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $parent = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['parent_id']);

                $study->setKulId($formData['kul_id'])
                    ->setTitle($formData['title'])
                    ->setPhase($formData['phase'])
                    ->setLanguage($formData['language'])
                    ->setParent($parent);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The study was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_study',
                    array(
                        'action' => 'edit',
                        'id' => $study->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'study' => $study,
                'mappings' => $mappings,
                'currentAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getMapping()))
            return new ViewModel();

        $this->getEntityManager()->remove($mapping);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $mappings = $this->_search($academicYear);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($mappings, $numResults);

        $result = array();
        foreach ($mappings as $mapping) {
            $item = (object) array();
            $item->mappingId = $mapping->getId();
            $item->id = $mapping->getStudy()->getId();
            $item->title = $mapping->getStudy()->getFullTitle();
            $item->phase = $mapping->getStudy()->getPhase();
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

        if (!($study = $this->_getStudy()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $subjects = $this->_searchSubject($study, $academicYear);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($subjects, $numResults);

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

    public function typeaheadAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
            return;

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllByTitleAndAcademicYearTypeAhead($this->getParam('string'), $academicYear);

        array_splice($studies, 20);

        $result = array();
        foreach ($studies as $study) {
            $item = (object) array();
            $item->id = $study->getId();
            $item->value = 'Phase ' . $study->getPhase() . '&mdash;' . $study->getFullTitle();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _search(AcademicYearEntity $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\AcademicYearMap')
                    ->findAllByTitleAndAcademicYear($this->getParam('string'), $academicYear);
        }
    }

    private function _searchSubject(Study $study, AcademicYearEntity $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByNameAndStudyAndAcademicYear($this->getParam('string'), $study, $academicYear);
            case 'code':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByCodeAndStudyAndAcademicYear($this->getParam('string'), $study, $academicYear);
        }
    }

    private function _getMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the mapping!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\AcademicYearMap')
            ->findOneById($this->getParam('id'));

        if (null === $mapping) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $mapping;
    }

    private function _getStudy()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the study!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $study = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findOneById($this->getParam('id'));

        if (null === $study) {
            $this->flashMessenger()->error(
                'Error',
                'No study with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $study;
    }

    private function _getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear'))
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }
}
