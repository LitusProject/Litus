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

namespace SecretaryBundle\Component\Controller;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\Person\Organization\AcademicYearMap;
use SecretaryBundle\Component\Registration\Articles as RegistrationArticles;
use SecretaryBundle\Entity\Syllabus\StudyEnrollment;
use SecretaryBundle\Entity\Syllabus\SubjectEnrollment;
use SecretaryBundle\Form\Registration\Subject\Add as AddSubjectForm;
use Zend\View\Model\ViewModel;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    protected function _studiesAction(Academic $academic, AcademicYear $academicYear)
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($academicYear);

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $studyIds = array();
        foreach($enrollments as $enrollment)
            $studyIds[] = $enrollment->getStudy()->getId();

        return new ViewModel(
            array(
                'studies' => $studies,
                'enrollments' => $studyIds,
            )
        );
    }

    protected function _saveStudiesAction(Academic $academic, AcademicYear $academicYear, $data)
    {
        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        $studies = array();

        if (!empty($data['studies'])) {
            foreach ($data['studies'] as $id) {
                if (isset($studies[$id]))
                    continue;

                $studies[$id] = true;

                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($id);
                $this->getEntityManager()->persist(new StudyEnrollment($academic, $academicYear, $study));

                $subjects = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($study, $academicYear);

                foreach ($subjects as $subject) {
                    if ($subject->isMandatory())
                        $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $subject->getSubject()));
                }
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @param Academic       $academic
     * @param AcademicYear   $academicYear
     * @param AddSubjectForm $form
     */
    protected function _subjectAction(Academic $academic, AcademicYear $academicYear, AddSubjectForm $form)
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $this->getEntityManager()->persist(
                    new SubjectEnrollment(
                        $academic,
                        $academicYear,
                        $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject')
                            ->findOneById($formData['subject_id'])
                    )
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The subject was succesfully added!'
                );

                $this->redirect()->toRoute(
                    'common_account',
                    array(
                        'action' => 'subjects',
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $mappings = array();
        $studySubjects = array();
        foreach ($enrollments as $enrollment) {
            $subjects = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $academicYear);
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects' => $subjects,
            );
            foreach($subjects as $subject)
                $studySubjects[] = $subject->getSubject()->getId();
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $subjectIds = array();
        $otherSubjects = array();
        foreach ($enrollments as $enrollment) {
            $subjectIds[] = $enrollment->getSubject()->getId();

            if (!in_array($enrollment->getSubject()->getId(), $studySubjects))
                $otherSubjects[] = $enrollment->getSubject();
        }

        return new ViewModel(
            array(
                'form' => $form,
                'mappings' => $mappings,
                'enrollments' => $subjectIds,
                'currentAcademicYear' => $academicYear,
                'otherSubjects' => $otherSubjects,
            )
        );
    }

    protected function _saveSubjectAction(Academic $academic, AcademicYear $academicYear, $data)
    {
        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach($enrollments as $enrollment)
            $this->getEntityManager()->remove($enrollment);

        $subjects = array();

        if (!empty($data['subjects'])) {
            foreach ($data['subjects'] as $id) {
                if (isset($subjects[$id]))
                    continue;

                $subjects[$id] = true;

                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject')
                    ->findOneById($id);
                $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $subject));
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @param  string $email
     * @return string
     */
    protected function _parseUniversityEmail($email)
    {
        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        return preg_replace('/[^a-z0-9\.@]/i', '', iconv("UTF-8", "US-ASCII//TRANSLIT", $email)).$studentDomain;
    }

    protected function _bookRegistrationArticles(Academic $academic, $tshirtSize, Organization $organization, AcademicYear $academicYear)
    {
        RegistrationArticles::book(
            $this->getEntityManager(),
            $academic,
            $organization,
            $academicYear,
            array(
                'payed' => false,
                'tshirtSize' => $tshirtSize,
            )
        );
    }

    protected function _getTermsAndConditions()
    {
        $termsAndConditions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions')
        );

        return $termsAndConditions[$this->getLanguage()->getAbbrev()];
    }

    protected function _setOrganization(Academic $academic, AcademicYear $academicYear, Organization $organization)
    {
        $map = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
            ->findOneByAcademicAndAcademicYear($academic, $academicYear);

        if (null === $map) {
            $this->getEntityManager()->persist(new AcademicYearMap($academic, $academicYear, $organization));
        } else {
            $map->setOrganization($organization);
        }

        $this->getEntityManager()->flush();
    }
}
