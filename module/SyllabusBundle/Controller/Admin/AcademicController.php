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
    CommonBundle\Entity\User\Person\Academic,
    SecretaryBundle\Entity\Syllabus\StudyEnrollment,
    SecretaryBundle\Entity\Syllabus\SubjectEnrollment,
    Zend\View\Model\ViewModel;

/**
 * AcademicController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AcademicController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (null !== $this->getParam('field')) {
            $academics = $this->search();

            $paginator = $this->paginator()->createFromArray(
                $academics,
                $this->getParam('page')
            );
        }

        if (!isset($paginator)) {
            $paginator = $this->paginator()->createFromEntity(
                'CommonBundle\Entity\User\Person\Academic',
                $this->getParam('page'),
                array(
                    'canLogin' => 'true',
                ),
                array(
                    'username' => 'ASC',
                )
            );
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function editAction()
    {
        if (!($academic = $this->getAcademicEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        return new ViewModel(
            array(
                'academic' => $academic,
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'studies' => $studies,
                'subjects' => $subjects,
            )
        );
    }

    public function deleteStudyAction()
    {
        $this->initAjax();

        if (!($study = $this->getStudyEnrollmentEntity())) {
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

    public function deleteSubjectAction()
    {
        $this->initAjax();

        if (!($subject = $this->getSubjectEnrollmentEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($subject);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function addStudyAction()
    {
        if (!($academic = $this->getAcademicEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $form = $this->getForm('syllabus_academic_study_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['study']['id']);

                $enrollment = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                    ->findOneByAcademicAndAcademicYearAndStudy($academic, $academicYear, $study);

                if (null === $enrollment) {
                    $this->getEntityManager()->persist(new StudyEnrollment($academic, $academicYear, $study));
                }

                $subjects = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($study, $academicYear);

                foreach ($subjects as $subject) {
                    if ($subject->isMandatory()) {
                        $enrollment = $this->getEntityManager()
                            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
                            ->findOneByAcademicAndAcademicYearAndSubject($academic, $academicYear, $subject->getSubject());

                        if (null === $enrollment) {
                            $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $subject->getSubject()));
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The study was successfully added!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_academic',
                    array(
                        'action' => 'edit',
                        'id' => $academic->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel(
                    array(
                        'academicYears' => $academicYears,
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'academic' => $academic,
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }

    public function addSubjectAction()
    {
        if (!($academic = $this->getAcademicEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $form = $this->getForm('syllabus_academic_subject_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject')
                    ->findOneById($formData['subject']['id']);

                $enrollment = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
                    ->findOneByAcademicAndAcademicYearAndSubject($academic, $academicYear, $subject);

                if (null === $enrollment) {
                    $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $subject));
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The subject was successfully added!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_academic',
                    array(
                        'action' => 'edit',
                        'id' => $academic->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel(
                    array(
                        'academicYears' => $academicYears,
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'academic' => $academic,
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $academics = $this->search();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($academics, $numResults);

        $result = array();
        foreach ($academics as $academic) {
            if ($academic->canLogin()) {
                $item = (object) array();
                $item->id = $academic->getId();
                $item->username = $academic->getUsername();
                $item->universityIdentification = (
                    null !== $academic->getUniversityIdentification() ? $academic->getUniversityIdentification() : ''
                );
                $item->fullName = $academic->getFullName();
                $item->email = $academic->getEmail();

                $result[] = $item;
            }
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return array|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByName($this->getParam('string'));
            case 'university_identification':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByUniversityIdentification($this->getParam('string'));
        }
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        $academic = $this->getEntityById('CommonBundle\Entity\User\Person\Academic');

        if (!($academic instanceof Academic)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academic;
    }

    /**
     * @return StudyEnrollment|null
     */
    private function getStudyEnrollmentEntity()
    {
        $enrollment = $this->getEntityById('SecretaryBundle\Entity\Syllabus\StudyEnrollment');

        if (!($enrollment instanceof StudyEnrollment)) {
            $this->flashMessenger()->error(
                'Error',
                'No study enrollment was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $enrollment;
    }

    /**
     * @return SubjectEnrollment|null
     */
    private function getSubjectEnrollmentEntity()
    {
        $enrollment = $this->getEntityById('SecretaryBundle\Entity\Syllabus\SubjectEnrollment');

        if (!($enrollment instanceof SubjectEnrollment)) {
            $this->flashMessenger()->error(
                'Error',
                'No study enrollment was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $enrollment;
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
                'syllabus_admin_academic',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
