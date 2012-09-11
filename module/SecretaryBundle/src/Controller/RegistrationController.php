<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    DateTime,
    Imagick,
    SecretaryBundle\Entity\Organization\MetaData,
    SecretaryBundle\Entity\Syllabus\StudyEnrollment,
    SecretaryBundle\Entity\Syllabus\SubjectEnrollment,
    SecretaryBundle\Form\Registration\Add as AddForm,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\View\Model\ViewModel;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function addAction()
    {
        $enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.registration_enabled');

        if ('1' !== $enabled)
            return $this->notFoundAction();

        $student = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneByUniversityIdentification($this->getParam('identification'));

        if (null !== $student) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'There is already a user with your university identification!'
                )
            );

            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add'
                )
            );

            return new ViewModel(
                array(
                    'registerShibbolethUrl' => $this->_getRegisterhibbolethUrl(),
                )
            );
        }

        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        if ($this->getRequest()->isPost()) {
            if (true ||$code->validate($this->getParam('hash'))) { // TODO: remove true
                $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'));

                $formData = $this->getRequest()->getPost();
                $formData['university_identification'] = $this->getParam('identification');
                $form->setData($formData);

                if ($form->isValid()) {
                    $roles = array(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('guest'),
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('student')
                    );

                    $student = new Academic(
                        $this->getParam('identification'),
                        $roles,
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['primary_email'] ? $formData['personal_email'] : $formData['university_email'],
                        $formData['phone_number'],
                        $formData['sex'],
                        $this->getParam('identification')
                    );

                    $primaryCity = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\City')
                        ->findOneById($formData['primary_address_address_city']);
                    $primaryStreet = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\Street')
                        ->findOneById($formData['primary_address_address_street' . $formData['primary_address_address_city']]);

                    $student->setBirthday(DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00'))
                        ->addUniversityStatus(
                            new UniversityStatus(
                                $student,
                                'student',
                                $this->getCurrentAcademicYear()
                            )
                        )
                        ->addOrganizationStatus(
                            new OrganizationStatus(
                                $student,
                                'member',
                                $this->getCurrentAcademicYear()
                            )
                        )
                        ->setPersonalEmail($formData['personal_email'])
                        ->setUniversityEmail($formData['university_email'])
                        ->setPrimaryAddress(
                            new Address(
                                $primaryStreet->getName(),
                                $formData['primary_address_address_number'],
                                $primaryCity->getPostal(),
                                $primaryCity->getName(),
                                'BE'
                            )
                        )
                        ->setSecondaryAddress(
                            new Address(
                                $formData['secondary_address_address_street'],
                                $formData['secondary_address_address_number'],
                                $formData['secondary_address_address_postal'],
                                $formData['secondary_address_address_city'],
                                $formData['secondary_address_address_country']
                            )
                        );

                    $filePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('common.profile_path');

                    $file = new FileTransfer();
                    if ($file->receive()) {
                        $image = new Imagick($file->getFileName());
                        $image->cropThumbnailImage(320, 240);

                        if ($student->getPhotoPath() != '' || $student->getPhotoPath() !== null) {
                            $fileName = $student->getPhotoPath();
                        } else {
                            $fileName = '';
                            do{
                                $fileName = '/' . sha1(uniqid());
                            } while (file_exists($filePath . $fileName));
                        }
                        $image->writeImage($filePath . $fileName);
                        $student->setPhotoPath($fileName);
                    }

                    $metaData = new MetaData(
                        $student,
                        $this->getCurrentAcademicYear(),
                        $formData['irreeel'],
                        $formData['bakske'],
                        $formData['tshirt_size']
                    );
                    $this->getEntityManager()->persist($metaData);

                    $student->activate(
                        $this->getEntityManager(),
                        $this->getMailTransport()
                    );

                    $this->getEntityManager()->persist($student);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'You are succesfully registered!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'secretary_registration',
                        array(
                            'action' => 'studies',
                            'identification' => $this->getParam('identification'),
                            'hash' => $this->getParam('hash'),
                        )
                    );

                    return new ViewModel();
                }

                return new ViewModel(
                    array(
                        'form' => $form,
                    )
                );
            }
        } else {
            if (null !== $code || true) { // TODO: remove true
                if (true || $code->validate($this->getParam('hash'))) { // TODO: remove true
                    $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'));

                    return new ViewModel(
                        array(
                            'form' => $form,
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'registerShibbolethUrl' => $this->_getRegisterhibbolethUrl(),
            )
        );
    }

    public function studiesAction()
    {
        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        $student = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneByUniversityIdentification($this->getParam('identification'));

        if ((null !== $code && $student !== null) || true) { // TODO: remove true
            if (true || $code->validate($this->getParam('hash'))) { // TODO: remove true
                $studies = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findAllParentsByAcademicYear($this->getCurrentAcademicYear());

                return new ViewModel(
                    array(
                        'studies' => $studies,
                    )
                );
            }
        }

        $this->redirect()->toRoute(
            'secretary_registration',
            array(
                'action' => 'add',
            )
        );

        return new ViewModel();
    }

    public function saveStudiesAction()
    {
        $this->initAjax();

        $data = $this->getRequest()->getPost();

        $student = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneByUniversityIdentification($this->getParam('identification'));

        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        if ((null !== $code && $student !== null) || true) { // TODO: remove true
            $enrollments = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                ->findAllByAcademicAndAcademicYear($student, $this->getCurrentAcademicYear());

            foreach($enrollments as $enrollment)
                $this->getEntityManager()->remove($enrollment);

            $enrollments = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
                ->findAllByAcademicAndAcademicYear($student, $this->getCurrentAcademicYear());

            foreach($enrollments as $enrollment)
                $this->getEntityManager()->remove($enrollment);

            if (!empty($data['studies'])) {
                foreach($data['studies'] as $id) {
                    $study = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Study')
                        ->findOneById($id);
                    $this->getEntityManager()->persist(new StudyEnrollment($student, $this->getCurrentAcademicYear(), $study));

                    $subjects = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                        ->findAllByStudyAndAcademicYear($study, $this->getCurrentAcademicYear());

                    foreach($subjects as $subject) {
                        if ($subject->isMandatory())
                            $this->getEntityManager()->persist(new SubjectEnrollment($student, $this->getCurrentAcademicYear(), $subject->getSubject()));
                    }
                }
            }
            $this->getEntityManager()->flush();

            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'success'),
                )
            );
        };

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'error'),
            )
        );
    }

    public function subjectsAction()
    {
        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        $student = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneByUniversityIdentification($this->getParam('identification'));

        if ((null !== $code && $student !== null) || true) { // TODO: remove true
            if (true || $code->validate($this->getParam('hash'))) { // TODO: remove true
                $enrollments = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                    ->findAllByAcademicAndAcademicYear($student, $this->getCurrentAcademicYear());

                $mappings = array();
                foreach($enrollments as $enrollment) {
                    $mappings[] = array(
                        'enrollment' => $enrollment,
                        'subjects' => $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                            ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $this->getCurrentAcademicYear())
                    );
                }

                return new ViewModel(
                    array(
                        'mappings' => $mappings,
                    )
                );
            }
        }

        $this->redirect()->toRoute(
            'secretary_registration',
            array(
                'action' => 'add',
            )
        );

        return new ViewModel();
    }

    public function saveSubjectsAction()
    {
        $this->initAjax();

        $data = $this->getRequest()->getPost();

        $student = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneByUniversityIdentification($this->getParam('identification'));

        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        if ((null !== $code && $student !== null) || true) { // TODO: remove true
            $enrollments = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
                ->findAllByAcademicAndAcademicYear($student, $this->getCurrentAcademicYear());

            foreach($enrollments as $enrollment)
                $this->getEntityManager()->remove($enrollment);

            if (!empty($data['subjects'])) {
                foreach($data['subjects'] as $id) {
                    $subject = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject')
                        ->findOneById($id);
                    $this->getEntityManager()->persist(new SubjectEnrollment($student, $this->getCurrentAcademicYear(), $subject));
                }
            }
            $this->getEntityManager()->flush();

            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'success'),
                )
            );
        };

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'error'),
            )
        );
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    private function _getRegisterhibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if ('%2F' != substr($shibbolethUrl, 0, -3))
            $shibbolethUrl .= '%2F';

        return $shibbolethUrl . '?source=register';
    }
}
