<?php

namespace SecretaryBundle\Component\Controller;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\Person\Organization\AcademicYearMap;
use CommonBundle\Entity\User\Status\University as UniversityStatus;
use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;
use SecretaryBundle\Component\Registration\Articles as RegistrationArticles;
use SecretaryBundle\Entity\Syllabus\Enrollment\Study as StudyEnrollment;
use SecretaryBundle\Entity\Syllabus\Enrollment\Subject as SubjectEnrollment;
use SecretaryBundle\Form\Registration\Subject\Add as AddSubjectForm;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    /**
     * @param Academic     $academic
     * @param AcademicYear $academicYear
     */
    protected function doStudiesAction(Academic $academic, AcademicYear $academicYear)
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllByAcademicYear($academicYear);

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $studyIds = array();
        foreach ($enrollments as $enrollment) {
            $studyIds[] = $enrollment->getStudy()->getId();
        }

        $date = new DateTime();
        $startOffset = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('start_academic_year_offset')
        );
        $endOffset = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.date_info_message_interval')
        );

        $startNewUniversityYear = $academicYear->getUniversityEndDate()->sub($startOffset);
        $startNewAcademicYear = $academicYear->getStartDate()->add(new DateInterval('P1Y'))->sub($endOffset);
        $dateInfoMessage = false;

        if ($date > $startNewAcademicYear && $date < $startNewUniversityYear) {
            $dateInfoMessage = true;
        }

        return new ViewModel(
            array(
                'studies'                => $studies,
                'enrollments'            => $studyIds,
                'academicYear'           => $academicYear,
                'startNewUniversityYear' => $startNewUniversityYear,
                'dateInfoMessage'        => $dateInfoMessage,
            )
        );
    }

    /**
     * @param Academic     $academic
     * @param AcademicYear $academicYear
     * @param array        $data
     */
    protected function doSaveStudiesAction(Academic $academic, AcademicYear $academicYear, $data)
    {
        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach ($enrollments as $enrollment) {
            $this->getEntityManager()->remove($enrollment);
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach ($enrollments as $enrollment) {
            $this->getEntityManager()->remove($enrollment);
        }

        $studies = array();
        if (isset($data['studies'])) {
            foreach ($data['studies'] as $id) {
                if (isset($studies[$id])) {
                    continue;
                }

                $studies[$id] = true;

                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($id);

                if ($study === null) {
                    continue;
                }

                $this->getEntityManager()->persist(new StudyEnrollment($academic, $study));

                $subjects = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                    ->findAllByStudy($study);

                foreach ($subjects as $subject) {
                    if ($subject->isMandatory()) {
                        $this->getEntityManager()->persist(new SubjectEnrollment($academic, $academicYear, $subject->getSubject()));
                    }
                }
            }
        }

        if (count($studies) > 0) {
            if ($academic->getUniversityStatus($academicYear) === null && $academic->canHaveUniversityStatus($academicYear)) {
                $academic->addUniversityStatus(
                    new UniversityStatus(
                        $academic,
                        'student',
                        $academicYear
                    )
                );
            }
        } else {
            if ($academic->getUniversityStatus($academicYear) !== null) {
                $academic->removeUniversityStatus($academic->getUniversityStatus($academicYear));
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
    protected function doSubjectAction(Academic $academic, AcademicYear $academicYear, AddSubjectForm $form)
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->getEntityManager()->persist(
                    new SubjectEnrollment(
                        $academic,
                        $academicYear,
                        $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject')
                            ->findOneById($formData['subject']['id'])
                    )
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The subject was succesfully added!'
                );

                $this->redirect()->toRoute(
                    $this->getParam('controller'),
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $mappings = array();
        $studySubjects = array();
        foreach ($enrollments as $enrollment) {
            $subjects = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                ->findAllByStudy($enrollment->getStudy());
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects'   => $subjects,
            );
            foreach ($subjects as $subject) {
                $studySubjects[] = $subject->getSubject()->getId();
            }
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $subjectIds = array();
        $otherSubjects = array();
        foreach ($enrollments as $enrollment) {
            $subjectIds[] = $enrollment->getSubject()->getId();

            if (!in_array($enrollment->getSubject()->getId(), $studySubjects)) {
                $otherSubjects[] = $enrollment->getSubject();
            }
        }

        return new ViewModel(
            array(
                'form'                => $form,
                'mappings'            => $mappings,
                'enrollments'         => $subjectIds,
                'currentAcademicYear' => $academicYear,
                'otherSubjects'       => $otherSubjects,
            )
        );
    }

    /**
     * @param Academic     $academic
     * @param AcademicYear $academicYear
     * @param array        $data
     */
    protected function doSaveSubjectAction(Academic $academic, AcademicYear $academicYear, $data)
    {
        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach ($enrollments as $enrollment) {
            $this->getEntityManager()->remove($enrollment);
        }

        $subjects = array();

        if (isset($data['subjects'])) {
            foreach ($data['subjects'] as $id) {
                if (isset($subjects[$id])) {
                    continue;
                }

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
    protected function parseUniversityEmail($email)
    {
        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        return preg_replace('/[^a-z0-9\.@]/i', '', iconv('UTF-8', 'US-ASCII//TRANSLIT', $email)) . $studentDomain;
    }

    /**
     * @param Academic     $academic
     * @param string       $tshirtSize
     * @param Organization $organization
     * @param AcademicYear $academicYear
     */
    protected function bookRegistrationArticles(Academic $academic, $tshirtSize, Organization $organization, AcademicYear $academicYear)
    {
        RegistrationArticles::book(
            $this->getEntityManager(),
            $academic,
            $organization,
            $academicYear,
            array(
                'payed'      => false,
                'tshirtSize' => $tshirtSize,
            )
        );
    }

    /**
     * @return string
     */
    protected function getTermsAndConditions()
    {
        $termsAndConditions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions')
        );

        return $termsAndConditions[$this->getLanguage()->getAbbrev()];
    }

    /**
     * @param Academic     $academic
     * @param AcademicYear $academicYear
     * @param Organization $organization
     */
    protected function setOrganization(Academic $academic, AcademicYear $academicYear, Organization $organization)
    {
        $map = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
            ->findOneByAcademicAndAcademicYear($academic, $academicYear);

        if ($map === null) {
            $this->getEntityManager()->persist(new AcademicYearMap($academic, $academicYear, $organization));
        } else {
            $map->setOrganization($organization);
        }
        $this->getEntityManager()->flush();
    }
}
