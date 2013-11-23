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

namespace CommonBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\PassKit\Pass\Membership,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\User\Credential,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    CommonBundle\Form\Account\Activate as ActivateForm,
    CommonBundle\Form\Account\Edit as EditForm,
    CommonBundle\Form\Account\FileServer\CreateAccount as CreateAccountForm,
    CommonBundle\Form\Account\FileServer\ChangePassword as ChangePasswordForm,
    CudiBundle\Entity\Sale\Booking,
    DateTime,
    SecretaryBundle\Entity\Organization\MetaData,
    SecretaryBundle\Entity\Registration,
    SecretaryBundle\Form\Registration\Subject\Add as SubjectForm,
    Zend\Http\Headers,
    Zend\Ldap\Attribute,
    Zend\Ldap\Ldap,
    Zend\View\Model\ViewModel;

/**
 * Handles account page.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AccountController extends \SecretaryBundle\Component\Controller\RegistrationController
{
    public function indexAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login first!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $mappings = array();
        foreach($studies as $enrollment) {
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects' => $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $this->getCurrentAcademicYear())
            );
        }

        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $subjectIds = array();
        foreach($subjects as $enrollment)
            $subjectIds[] = $enrollment->getSubject()->getId();

        return new ViewModel(
            array(
                'academicYear' => $this->getCurrentAcademicYear(),
                'metaData' => $metaData,
                'studies' => $mappings,
                'subjects' => $subjectIds,
            )
        );
    }

    public function editAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login first!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        $registrationEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.registration_enabled') == '1';

        $academic = $this->getAuthentication()->getPersonObject();

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $termsAndConditions = $this->_getTermsAndConditions();

        $form = new EditForm(
            $academic,
            $this->getCurrentAcademicYear(),
            $metaData,
            $this->getCache(),
            $this->getEntityManager(),
            $this->getParam('identification')
        );

        $ids = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $membershipArticles = array();
        foreach($ids as $organization => $id) {
            $membershipArticles[$organization] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            $formData['university_identification'] = $this->getParam('identification');
            if ($metaData && $metaData->becomeMember())
                $formData['become_member'] = true;
            else
                $formData['become_member'] = isset($formData['become_member']) ? $formData['become_member'] : false;
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $universityEmail = $this->_parseUniversityEmail($formData['university_email']);

                $academic->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['primary_email'] ? $formData['personal_email'] : $universityEmail)
                    ->setPhoneNumber($formData['phone_number'])
                    ->setSex($formData['sex'])
                    ->setBirthday(DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00'))
                    ->setPersonalEmail($formData['personal_email'])
                    ->setUniversityEmail($universityEmail);

                $primaryAddress = $this->_getPrimaryAddress($formData);

                if (null !== $academic->getPrimaryAddress()) {
                    $academic->getPrimaryAddress()
                        ->setStreet($primaryAddress->getStreet())
                        ->setNumber($primaryAddress->getNumber())
                        ->setMailbox($primaryAddress->getMailbox())
                        ->setPostal($primaryAddress->getPostal())
                        ->setCity($primaryAddress->getCity())
                        ->setCountry($primaryAddress->getCountry());
                } else {
                    $academic->setPrimaryAddress($primaryAddress);
                }

                if (null !== $academic->getSecondaryAddress()) {
                    $academic->getSecondaryAddress()
                        ->setStreet($formData['secondary_address_address_street'])
                        ->setNumber($formData['secondary_address_address_number'])
                        ->setMailbox($formData['secondary_address_address_mailbox'])
                        ->setPostal($formData['secondary_address_address_postal'])
                        ->setCity($formData['secondary_address_address_city'])
                        ->setCountry($formData['secondary_address_address_country']);
                } else {
                    $academic->setSecondaryAddress(
                        new Address(
                            $formData['secondary_address_address_street'],
                            $formData['secondary_address_address_number'],
                            $formData['primary_address_address_mailbox'],
                            $formData['secondary_address_address_postal'],
                            $formData['secondary_address_address_city'],
                            $formData['secondary_address_address_country']
                        )
                    );
                }

                if ($academic->canHaveUniversityStatus($this->getCurrentAcademicYear())) {
                    $status = new UniversityStatus(
                        $academic,
                        'student',
                        $this->getCurrentAcademicYear()
                    );
                    $academic->addUniversityStatus($status);
                }

                $this->_uploadProfileImage($academic);
                if (isset($formData['organization'])) {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOneById($formData['organization']);

                    $this->_setOrganization(
                        $academic,
                        $this->getCurrentAcademicYear(),
                        $organization
                    );
                } else {
                    $organization = current(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Organization')
                            ->findAll()
                    );
                }

                $tshirts = unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.tshirt_article')
                );

                if (null !== $metaData) {
                    if ($registrationEnabled) {
                        if (null !== $metaData->getTshirtSize()) {
                            $booking = $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Booking')
                                ->findOneAssignedByArticleAndPerson(
                                    $this->getEntityManager()
                                        ->getRepository('CudiBundle\Entity\Sale\Article')
                                        ->findOneById($tshirts[$metaData->getTshirtSize()]),
                                    $academic
                                );

                            if ($booking !== null)
                                $this->getEntityManager()->remove($booking);
                        }
                        $becomeMember = $metaData->becomeMember() ? true : $formData['become_member'];
                    } else {
                        $becomeMember = $metaData->becomeMember();
                    }

                    if ($becomeMember) {
                        if ($registrationEnabled) {
                            $metaData->setBecomeMember($becomeMember)
                                ->setTshirtSize($formData['tshirt_size']);
                        }

                        $metaData->setReceiveIrReeelAtCudi($formData['irreeel']);
                    }

                    $metaData->setBakskeByMail($formData['bakske']);
                } elseif ($registrationEnabled) {
                    if ($formData['become_member']) {
                        $metaData = new MetaData(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $formData['become_member'],
                            $formData['irreeel'],
                            $formData['bakske'],
                            $formData['tshirt_size']
                        );
                    } else {
                        $metaData = new MetaData(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $formData['become_member'],
                            false,
                            $formData['bakske'],
                            null
                        );
                    }

                    $this->getEntityManager()->persist($metaData);
                }

                if ($registrationEnabled) {
                    $membershipArticles = array();
                    $ids = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('secretary.membership_article')
                    );

                    foreach($ids as $organizationId => $articleId) {
                        $membershipArticles[$organizationId] = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($articleId);
                    }

                    if ($metaData->becomeMember()) {
                        $this->_bookRegistrationArticles($academic, $formData['tshirt_size'], $organization, $this->getCurrentAcademicYear());
                    } else {
                        foreach($membershipArticles as $membershipArticle) {
                            $booking = $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Booking')
                                ->findOneSoldOrAssignedOrBookedByArticleAndPerson(
                                    $membershipArticle,
                                    $academic
                                );

                            if (null !== $booking)
                                $this->getEntityManager()->remove($booking);
                        }
                    }
                }

                $academic->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport()
                );

                $registration = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

                if (null === $registration) {
                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->getEntityManager()->persist($registration);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'Your data was succesfully updated!'
                    )
                );

                $this->_doRedirect();

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'termsAndConditions' => $termsAndConditions,
                'studentDomain' => $studentDomain,
                'membershipArticles' => $membershipArticles,
            )
        );
    }

    public function studiesAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login first!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        return $this->_studiesAction(
            $this->getAuthentication()->getPersonObject(),
            $this->getCurrentAcademicYear()
        );
    }

    public function saveStudiesAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login first!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        $this->initAjax();

        return $this->_saveStudiesAction(
            $this->getAuthentication()->getPersonObject(),
            $this->getCurrentAcademicYear(),
            $this->getRequest()->getPost()
        );
    }

    public function subjectsAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login first!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        return $this->_subjectAction(
            $this->getAuthentication()->getPersonObject(),
            $this->getCurrentAcademicYear(),
            new SubjectForm()
        );
    }

    public function saveSubjectsAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login first!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        $this->initAjax();

        return $this->_saveSubjectAction(
            $this->getAuthentication()->getPersonObject(),
            $this->getCurrentAcademicYear(),
            $this->getRequest()->getPost()
        );
    }

    public function activateAction()
    {
        if (!($user = $this->_getUser()))
            return new ViewModel();

        $form = new ActivateForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $user->setCode(null)
                    ->setCredential(
                        new Credential(
                            'sha512',
                            $formData['credential']
                        )
                    );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'Your account was succesfully activated!'
                    )
                );

                $this->redirect()->toRoute(
                    'common_index'
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function photoAction() {
        $imagePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path') . '/' . $this->getParam('image');

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="' . $this->getParam('image') . '"',
            'Content-Type' => mime_content_type($imagePath),
            'Content-Length' => filesize($imagePath),
        ));
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($imagePath, 'r');
        $data = fread($handle, filesize($imagePath));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    public function fileServerAction() {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login first!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        if ('' == $this->getAuthentication()->getPersonObject()->getUniversityIdentification()) {
            return new ViewModel(
                array(
                    'noUniversityIdentification' => true
                )
            );
        }

        $registration = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findOneByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        if (null !== $registration && $registration->hasPayed()) {
            $this->getLdap()->bind();

            $peopleOu = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.ldap_people_ou');
            $studentsOu = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.ldap_students_ou');
            $studentsCn = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.ldap_students_cn');
            $usersCn = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.ldap_users_cn');

            if ($accountExists = $this->getLdap()->exists('uid=' . $this->getAuthentication()->getPersonObject()->getUniversityIdentification() . ',' . $studentsOu)) {
                $form = new ChangePasswordForm();

                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    $form->setData($formData);

                    if ($form->isValid()) {
                        $entry = $this->getLdap()->getEntry('uid=' . $this->getAuthentication()->getPersonObject()->getUniversityIdentification() . ',' . $studentsOu);

                        $salt = mcrypt_create_iv(8, MCRYPT_DEV_URANDOM);
                        Attribute::setAttribute(
                            $entry, 'userPassword', '{SSHA}' . base64_encode(sha1($formData['password'] . $salt, true) . $salt)
                        );

                        if ('production' == getenv('APPLICATION_ENV')) {
                            $this->getLdap()->update(
                                'uid=' . $this->getAuthentication()->getPersonObject()->getUniversityIdentification() . ',' . $studentsOu, $entry
                            );
                        }

                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::SUCCESS,
                                'Success',
                                'Your password was succesfully changed!'
                            )
                        );

                        $this->redirect()->toRoute(
                            'common_account',
                            array(
                                'action' => 'fileServer',
                            )
                        );

                        return new ViewModel();
                    }
                }
            } else {
                $form = new CreateAccountForm();

                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    $form->setData($formData);

                    if ($form->isValid()) {
                        $uidNumbers = $this->getLdap()->search(
                            'uidNumber=*',
                            $peopleOu,
                            Ldap::SEARCH_SCOPE_SUB,
                            array(
                                'uidNumber'
                            )
                        );

                        $maxUidNumber = 0;
                        foreach ($uidNumbers as $uidNumber) {
                            if ($uidNumber['uidnumber'][0] == 65534)
                                continue;

                            if ($uidNumber['uidnumber'][0] > $maxUidNumber)
                                $maxUidNumber = $uidNumber['uidnumber'][0];
                        }

                        $studentsGroup = $this->getLdap()->getEntry($studentsCn);
                        $usersGroup = $this->getLdap()->getEntry($usersCn);

                        // Creating our new user
                        $newEntry = array();

                        Attribute::setAttribute(
                            $newEntry,
                            'objectClass',
                            array(
                                'posixAccount',
                                'inetOrgPerson',
                                'organizationalPerson',
                                'person'
                            )
                        );

                        Attribute::setAttribute(
                            $newEntry, 'cn', $this->getAuthentication()->getPersonObject()->getFullName()
                        );
                        Attribute::setAttribute(
                            $newEntry, 'gidNumber', $usersGroup['gidnumber'][0]
                        );
                        Attribute::setAttribute(
                            $newEntry, 'givenName', $this->getAuthentication()->getPersonObject()->getFirstName()
                        );
                        Attribute::setAttribute(
                            $newEntry, 'homeDirectory', '/vtk/students/' . $this->getAuthentication()->getPersonObject()->getUniversityIdentification()
                        );
                        Attribute::setAttribute(
                            $newEntry, 'loginShell', '/bin/false'
                        );

                        Attribute::setAttribute(
                            $newEntry, 'sn', $this->getAuthentication()->getPersonObject()->getLastName()
                        );

                        Attribute::setAttribute(
                            $newEntry, 'uid', $this->getAuthentication()->getPersonObject()->getUniversityIdentification()
                        );
                        Attribute::setAttribute(
                            $newEntry, 'uidNumber', ++$maxUidNumber
                        );

                        $salt = mcrypt_create_iv(8, MCRYPT_DEV_URANDOM);
                        Attribute::setAttribute(
                            $newEntry, 'userPassword', '{SSHA}' . base64_encode(sha1($formData['password'] . $salt, true) . $salt)
                        );

                        if ('production' == getenv('APPLICATION_ENV')) {
                            $this->getLdap()->add(
                                'uid=' . $this->getAuthentication()->getPersonObject()->getUniversityIdentification() . ',' . $studentsOu, $newEntry
                            );
                        }

                        // Add the user to the group
                        $memberUidArray = $studentsGroup['memberuid'];

                        $memberUidArray[] = $this->getAuthentication()->getPersonObject()->getUniversityIdentification();
                        Attribute::setAttribute(
                            $studentsGroup, 'memberUid', $memberUidArray
                        );

                        if ('production' == getenv('APPLICATION_ENV')) {
                            $this->getLdap()->update(
                                $studentsCn, $studentsGroup
                            );
                        }

                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::SUCCESS,
                                'Success',
                                'Your account was successfully created! Please note that it may take a few minutes before your account is accessible.'
                            )
                        );

                        $this->redirect()->toRoute(
                            'common_account',
                            array(
                                'action' => 'fileServer',
                            )
                        );

                        return new ViewModel();
                    }
                }
            }

            return new ViewModel(
                array(
                    'hasPayed' => true,
                    'accountExists' => $accountExists,
                    'form' => $form
                )
            );
        } else {
            return new ViewModel(
                array(
                    'hasPayed' => false
                )
            );
        }
    }

    public function passbookAction()
    {
        $pass = new TmpFile();
        $membership = new Membership(
            $this->getEntityManager(),
            $this->getAuthentication()->getPersonObject(),
            $this->getCurrentAcademicYear(),
            $pass,
            'data/images/pass_kit'
        );
        $membership->createPass();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="membership.pkpass"',
            'Content-Type'        => 'application/vnd.apple.pkpass',
            'Content-Length'      => filesize($pass->getFileName()),
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $pass->getContent()
            )
        );
    }

    private function _getUser()
    {
        if (null === $this->getParam('code')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No code was given to identify the user!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return;
        }

        $user = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Code')
            ->findOnePersonByCode($this->getParam('code'));

        if (null === $user) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given code is not valid!'
                )
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return;
        }

        return $user;
    }

    private function _doRedirect() {
        if (null === $this->getParam('return')) {
            $this->redirect()->toRoute(
                'common_account'
            );
        } else {
            $this->redirect()->toRoute(
                $this->getParam('return')
            );
        }
    }
}
