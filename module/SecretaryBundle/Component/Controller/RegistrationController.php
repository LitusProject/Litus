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

namespace SecretaryBundle\Component\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\Organization,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Entity\User\Person\Organization\AcademicYearMap,
    CudiBundle\Entity\Sale\Booking,
    DateInterval,
    DateTime,
    Imagick,
    SecretaryBundle\Entity\Syllabus\StudyEnrollment,
    SecretaryBundle\Entity\Syllabus\SubjectEnrollment,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\IsImage as ImageValidator,
    Zend\Mvc\MvcEvent,
    Zend\View\Model\ViewModel;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $_academicYear;

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
            foreach($data['studies'] as $id) {
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

                foreach($subjects as $subject) {
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

    protected function _subjectAction(Academic $academic, AcademicYear $academicYear, $form)
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

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The subject was succesfully added!'
                    )
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
        foreach($enrollments as $enrollment) {
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
        foreach($enrollments as $enrollment) {
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
            foreach($data['subjects'] as $id) {
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

    protected function _parseUniversityEmail($email)
    {
        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        return preg_replace('/[^a-z0-9\.@]/i', '', iconv("UTF-8", "US-ASCII//TRANSLIT", $email)) . $studentDomain;
    }

    protected function _uploadProfileImage(Academic $academic)
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path');

        $upload = new FileUpload();
        $upload->addValidator(new SizeValidator(array('max' => '3MB')));
        $upload->addValidator(new ImageValidator());

        if ($upload->isValid()) {
            $upload->receive();

            $image = new Imagick($upload->getFileName());
            unlink($upload->getFileName());
            $image->cropThumbnailImage(320, 240);

            if ($academic->getPhotoPath() != '' || $academic->getPhotoPath() !== null) {
                $fileName = $academic->getPhotoPath();
            } else {
                $fileName = '';
                do{
                    $fileName = sha1(uniqid());
                } while (file_exists($filePath . '/' . $fileName));
            }
            $image->writeImage($filePath . '/' . $fileName);
            $academic->setPhotoPath($fileName);
        }
    }

    protected function _bookRegistrationArticles(Academic $academic, $tshirtSize, AcademicYear $academicYear)
    {
        $organizationMap = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
            ->findOneByAcademicAndAcademicYear($academic, $academicYear);

        if (null !== $organizationMap) {
            $organization = $organizationMap->getOrganization();
        } else {
            $organization = current($this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findAll());
        }

        $ids = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        if (isset($ids[$organization->getId()])) {
            $membershipArticle = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($ids[$organization->getId()]);

            $booking = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPerson(
                    $membershipArticle,
                    $academic
                );

            if (null === $booking) {
                $booking = new Booking(
                    $this->getEntityManager(),
                    $academic,
                    $membershipArticle,
                    'assigned',
                    1,
                    true
                );

                $this->getEntityManager()->persist($booking);
            }
        }

        $tshirts = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.tshirt_article')
        );

        $hasShirt = false;
        foreach ($tshirts as $tshirt) {
            $booking = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPerson(
                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($tshirt),
                    $academic
                );

            if (null !== $booking) {
                $hasShirt = true;
                break;
            }
        }

        $enableAssignment = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_automatic_assignment');
        $currentPeriod = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $currentPeriod->setEntityManager($this->getEntityManager());

        if (!$hasShirt) {
            $booking = new Booking(
                $this->getEntityManager(),
                $academic,
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($tshirts[$tshirtSize]),
                'booked',
                1,
                true
            );

            $this->getEntityManager()->persist($booking);

            if ($enableAssignment == '1') {
                $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                if ($available > 0) {
                    if ($available >= $booking->getNumber()) {
                        $booking->setStatus('assigned', $this->getEntityManager());
                    }
                }
            }
        }

        $registrationArticles = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.registration_articles')
        );

        foreach ($registrationArticles as $registrationArticle) {
            $booking = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPerson(
                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($registrationArticle),
                    $academic
                );

            // Already got this article, continue
            if (null !== $booking)
                continue;

            $booking = new Booking(
                $this->getEntityManager(),
                $academic,
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($registrationArticle),
                'booked',
                1,
                true
            );
            $this->getEntityManager()->persist($booking);

            if ($enableAssignment == '1') {
                $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                if ($available > 0) {
                    if ($available >= $booking->getNumber()) {
                        $booking->setStatus('assigned', $this->getEntityManager());
                    }
                }
            }
        }
    }

    protected function _getTermsAndConditions()
    {
        try {
            $termsAndConditions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions_' . $this->getLanguage()->getAbbrev());
        } catch(\Exception $e) {
            $termsAndConditions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.terms_and_conditions_' . \Locale::getDefault());
        }
        return $termsAndConditions;
    }

    protected function _getPrimaryAddress($formData)
    {
        if ($formData['primary_address_address_city'] != 'other') {
            $primaryCity = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\City')
                ->findOneById($formData['primary_address_address_city']);
            $primaryCityName = $primaryCity->getName();
            $primaryPostal = $primaryCity->getPostal();
            $street = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\Street')
                ->findOneById($formData['primary_address_address_street_' . $formData['primary_address_address_city']]);
            $primaryStreet = $street ? $street->getName() : '';
        } else {
            $primaryCityName = $formData['primary_address_address_city_other'];
            $primaryStreet = $formData['primary_address_address_street_other'];
            $primaryPostal = $formData['primary_address_address_postal_other'];
        }

        return new Address(
            $primaryStreet,
            $formData['primary_address_address_number'],
            $formData['primary_address_address_mailbox'],
            $primaryPostal,
            $primaryCityName,
            'BE'
        );
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

    /**
     * Get the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getCurrentAcademicYear($organization = false)
    {
        if (null !== $this->_academicYear)
            return $this->_academicYear;

        $start = new DateTime();
        $start->add(
            new DateInterval(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.registration_open_before_academic_year')
            )
        );

        $startAcademicYear = AcademicYearUtil::getStartOfAcademicYear($start);
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYear($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }

        $this->_academicYear = $academicYear;

        return $academicYear;
    }
}