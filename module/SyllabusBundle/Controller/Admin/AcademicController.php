<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    SecretaryBundle\Entity\Syllabus\StudyEnrollment,
    SecretaryBundle\Entity\Syllabus\SubjectEnrollment,
    SyllabusBundle\Form\Admin\Academic\Study\Add as StudyForm,
    SyllabusBundle\Form\Admin\Academic\Subject\Add as SubjectForm,
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
            $academics = $this->_search();

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
                    'canLogin' => 'true'
                ),
                array(
                    'username' => 'ASC'
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
        if (!($academic = $this->_getAcademic()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

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

        if (!($study = $this->_getStudyEnrollment()))
            return new ViewModel();

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

        if (!($subject = $this->_getSubjectEnrollment()))
            return new ViewModel();

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
        if (!($academic = $this->_getAcademic()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $form = new StudyForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['study_id']);

                $enrollment = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                    ->findOneByAcademicAndAcademicYearAndStudy($academic, $academicYear, $study);
                if (null === $enrollment)
                    $this->getEntityManager()->persist(new StudyEnrollment($academic, $academicYear, $study));

                $subjects = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($study, $academicYear);

                foreach($subjects as $subject) {
                    if ($subject->isMandatory()) {
                        $enrollment = $this->getEntityManager()
                            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
                            ->findOneByAcademicAndAcademicYearAndSubject($academic, $academicYear, $subject->getSubject());

                        if (null === $enrollment)
                            $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $subject->getSubject()));
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The study was successfully added!'
                    )
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
        if (!($academic = $this->_getAcademic()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $form = new SubjectForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject')
                    ->findOneById($formData['subject_id']);

                $enrollment = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
                    ->findOneByAcademicAndAcademicYearAndSubject($academic, $academicYear, $subject);

                if (null === $enrollment)
                    $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $subject));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The subject was successfully added!'
                    )
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

        $academics = $this->_search();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($academics, $numResults);

        $result = array();
        foreach($academics as $academic) {
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

    private function _search()
    {
        switch($this->getParam('field')) {
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

    private function _getAcademic()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the academic!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->getParam('id'));

        if (null === $academic) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academic;
    }

    private function _getStudyEnrollment()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the study!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $study = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findOneById($this->getParam('id'));

        if (null === $study) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No study with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $study;
    }

    private function _getSubjectEnrollment()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the subject!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $subject = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findOneById($this->getParam('id'));

        if (null === $subject) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No study with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $subject;
    }

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            return $this->getCurrentAcademicYear();
        } else {
            $startAcademicYear = AcademicYear::getDateTime($this->getParam('academicyear'));

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );
        }
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
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
