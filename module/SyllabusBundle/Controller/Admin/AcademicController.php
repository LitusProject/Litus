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

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use CommonBundle\Entity\User\Person\Academic;
use SecretaryBundle\Entity\Syllabus\Enrollment\Study as StudyEnrollment;
use SecretaryBundle\Entity\Syllabus\Enrollment\Subject as SubjectEnrollment;
use Laminas\View\Model\ViewModel;

/**
 * AcademicController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AcademicController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = null;
        if ($this->getParam('field') !== null) {
            $academics = $this->search();

            $paginator = $this->paginator()->createFromArray(
                $academics,
                $this->getParam('page')
            );
        }

        if ($paginator === null) {
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
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function editAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        return new ViewModel(
            array(
                'academic'            => $academic,
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'studies'             => $studies,
                'subjects'            => $subjects,
            )
        );
    }

    public function deleteStudyAction()
    {
        $this->initAjax();

        $study = $this->getStudyEnrollmentEntity();
        if ($study === null) {
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

        $subject = $this->getSubjectEnrollmentEntity();
        if ($subject === null) {
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
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
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
                    ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
                    ->findOneByAcademicAndStudy($academic, $study);

                if ($enrollment === null) {
                    $this->getEntityManager()->persist(new StudyEnrollment($academic, $study));
                }

                $mappings = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                    ->findAllByStudy($study);

                foreach ($mappings as $mapping) {
                    if ($mapping->isMandatory()) {
                        $enrollment = $this->getEntityManager()
                            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
                            ->findOneByAcademicAndAcademicYearAndSubject($academic, $academicYear, $mapping->getSubject());

                        if ($enrollment === null) {
                            $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $mapping->getSubject()));
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
                        'action'       => 'edit',
                        'id'           => $academic->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academic'            => $academic,
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
            )
        );
    }

    public function addSubjectAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
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
                    ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
                    ->findOneByAcademicAndAcademicYearAndSubject($academic, $academicYear, $subject);

                if ($enrollment === null) {
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
                        'action'       => 'edit',
                        'id'           => $academic->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academic'            => $academic,
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
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
                $item->universityIdentification = ($academic->getUniversityIdentification() ?? '');
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
     * @return array
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

        return array();
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
        $enrollment = $this->getEntityById('SecretaryBundle\Entity\Syllabus\Enrollment\Study');

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
        $enrollment = $this->getEntityById('SecretaryBundle\Entity\Syllabus\Enrollment\Subject');

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
