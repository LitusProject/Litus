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
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $studies = $this->search($academicYear);
        }

        if (!isset($studies)) {
            $studies = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study')
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $studies,
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

        $form = $this->getForm('syllabus_study_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $study = $form->hydrateObject();

                $this->getEntityManager()->persist($study);

                $study->setAcademicYear($academicYear);

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

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'form' => $form,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function editAction()
    {
        if (!($study = $this->getStudyEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $mappings = $this->searchSubject($study);
        }

        if (!isset($mappings)) {
            $mappings = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                ->findAllByStudy($study);
        }

        $form = $this->getForm('syllabus_study_edit', array('study' => $study));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
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
                'currentAcademicYear' => $study->getAcademicYear(),
                'academicYears' => $academicYears,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($study = $this->getStudyEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($study);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $studies = $this->search($academicYear)
            ->getResult();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($studies, $numResults);

        $result = array();
        foreach ($studies as $study) {
            $item = (object) array();
            $item->id = $study->getId();
            $item->title = $study->getCombination()->getTitle();
            $item->phase = $study->getCombination()->getPhase();
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

        if (!($study = $this->getStudyEntity())) {
            return new ViewModel();
        }

        $subjects = $this->searchSubject($study)
            ->getResult();

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
            $item->students = $subject->getSubject()->getNbEnrollment($study->getAcademicYear());
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
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return;
        }

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

    /**
     * @param  AcademicYearEntity       $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function search(AcademicYearEntity $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $academicYear);
        }
    }

    /**
     * @param  Study                    $study
     * @return \Doctrine\ORM\Query|null
     */
    private function searchSubject(Study $study)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                    ->findAllByNameAndStudyQuery($this->getParam('string'), $study);
            case 'code':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                    ->findAllByCodeAndStudyQuery($this->getParam('string'), $study);
        }
    }

    /**
     * @return Study|null
     */
    private function getStudyEntity()
    {
        $study = $this->getEntityById('SyllabusBundle\Entity\Study');

        if (!($study instanceof Study)) {
            $this->flashMessenger()->error(
                'Error',
                'No study was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $study;
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
