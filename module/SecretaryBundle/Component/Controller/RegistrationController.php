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

namespace SecretaryBundle\Component\Controller;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\Person\Organization\AcademicYearMap;
use CommonBundle\Entity\User\Status\University as UniversityStatus;
use DateInterval;
use DateTime;
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach ($enrollments as $enrollment) {
            $this->getEntityManager()->remove($enrollment);
        }

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach ($enrollments as $enrollment) {
            $this->getEntityManager()->remove($enrollment);
        }

        $studies = array();

        if (!empty($data['studies'])) {
            foreach ($data['studies'] as $id) {
                if (isset($studies[$id])) {
                    continue;
                }

                $studies[$id] = true;

                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($id);

                if (null === $study) {
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

            if (null === $academic->getUniversityStatus($academicYear) && $academic->canHaveUniversityStatus($academicYear)) {
                $academic->addUniversityStatus(
                    new UniversityStatus(
                        $academic,
                        'student',
                        $academicYear
                    )
                );
            }
        } else {
            if (null !== $academic->getUniversityStatus($academicYear)) {
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        foreach ($enrollments as $enrollment) {
            $this->getEntityManager()->remove($enrollment);
        }

        $subjects = array();

        if (!empty($data['subjects'])) {
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
     * @param Organization $organization
     * @param AcademicYear $academicYear
     */
    protected function bookRegistrationArticles(Academic $academic, Organization $organization, AcademicYear $academicYear)
    {
        RegistrationArticles::book(
            $this->getEntityManager(),
            $academic,
            $organization,
            $academicYear,
            array(
                'payed' => false,
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

        if (null === $map) {
            $this->getEntityManager()->persist(new AcademicYearMap($academic, $academicYear, $organization));
        } else {
            $map->setOrganization($organization);
        }

        $this->getEntityManager()->flush();
    }
}
