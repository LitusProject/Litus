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

namespace SecretaryBundle\Controller;

use CommonBundle\Component\Authentication\Authentication,
    CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    DateTime,
    SecretaryBundle\Entity\Organization\MetaData,
    SecretaryBundle\Entity\Registration,
    SecretaryBundle\Form\Registration\Add as AddForm,
    SecretaryBundle\Form\Registration\Edit as EditForm,
    SecretaryBundle\Form\Registration\Subject\Add as SubjectForm,
    Zend\Mvc\MvcEvent,
    Zend\View\Model\ViewModel;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \SecretaryBundle\Component\Controller\RegistrationController
{
    /**
     * Execute the request.
     *
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.registration_enabled');

        if ('1' !== $enabled) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        return $result;
    }

    public function addAction()
    {
        if (null !== $this->getParam('identification')) {
            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($this->getParam('identification'));
        } else {
            $academic = null;
        }

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $termsAndConditions = $this->_getTermsAndConditions();

        if (null !== $academic) {
            $this->_authenticate();

            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'edit',
                )
            );

            return new ViewModel();
        }

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if ($this->getRequest()->isPost()) {
            if ($this->_isValidCode()) {
                $code = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                    ->findLastByUniversityIdentification($this->getParam('identification'));

                $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'), unserialize($code->getInfo()));

                $formData = $this->getRequest()->getPost();
                $formData['university_identification'] = $this->getParam('identification');
                $form->setData($formData);

                if ($form->isValid()) {
                    $formData = $form->getFormData($formData);

                    $roles = array(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('guest'),
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('student')
                    );

                    $universityEmail = $this->_parseUniversityEmail($formData['university_email']);

                    $academic = new Academic(
                        $this->getParam('identification'),
                        $roles,
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['primary_email'] ? $formData['personal_email'] : $universityEmail,
                        $formData['phone_number'],
                        $formData['sex'],
                        $this->getParam('identification')
                    );

                    $academic->setBirthday(DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00'))
                        ->addUniversityStatus(
                            new UniversityStatus(
                                $academic,
                                'student',
                                $this->getCurrentAcademicYear()
                            )
                        )
                        ->setPersonalEmail($formData['personal_email'])
                        ->setUniversityEmail($universityEmail)
                        ->setPrimaryAddress(
                            $this->_getPrimaryAddress($formData)
                        )
                        ->setSecondaryAddress(
                            new Address(
                                $formData['secondary_address_address_street'],
                                $formData['secondary_address_address_number'],
                                $formData['secondary_address_address_mailbox'],
                                $formData['secondary_address_address_postal'],
                                $formData['secondary_address_address_city'],
                                $formData['secondary_address_address_country']
                            )
                        );

                    $this->_uploadProfileImage($academic);
                    if (isset($formData['organization'])) {
                        $this->_setOrganization(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Organization')
                                ->findOneById($formData['organization'])
                        );
                    }

                    if ($formData['become_member']) {
                        $metaData = new MetaData(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $formData['become_member'],
                            $formData['irreeel'],
                            $formData['bakske'],
                            $formData['tshirt_size']
                        );

                        $this->_bookRegistrationArticles($academic, $formData['tshirt_size'], $this->getCurrentAcademicYear());
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

                    $academic->activate(
                        $this->getEntityManager(),
                        $this->getMailTransport()
                    );

                    $this->getEntityManager()->persist($academic);

                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->getEntityManager()->persist($registration);

                    $this->getEntityManager()->flush();

                    $authentication = new Authentication(
                        new ShibbolethAdapter(
                            $this->getEntityManager(),
                            'CommonBundle\Entity\User\Person\Academic',
                            'universityIdentification'
                        ),
                        $this->getServiceLocator()->get('authentication_doctrineservice')
                    );

                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();

                    $authentication->authenticate(
                        $this->getParam('identification'), '', true
                    );

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
                        )
                    );

                    return new ViewModel();
                }

                return new ViewModel(
                    array(
                        'form' => $form,
                        'termsAndConditions' => $termsAndConditions,
                        'studentDomain' => $studentDomain,
                    )
                );
            }
        } else {
            if ($this->_isValidCode()) {
                $code = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                    ->findLastByUniversityIdentification($this->getParam('identification'));

                $form = new AddForm($this->getCache(), $this->getEntityManager(), $this->getParam('identification'), unserialize($code->getInfo()));

                return new ViewModel(
                    array(
                        'form' => $form,
                        'termsAndConditions' => $termsAndConditions,
                        'studentDomain' => $studentDomain,
                        'organizations' => $organizations,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'registerShibbolethUrl' => $this->_getRegisterhibbolethUrl(),
            )
        );
    }

    public function editAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

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
                            $formData['secondary_address_address_mailbox'],
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
                    $this->_setOrganization(
                        $academic,
                        $this->getCurrentAcademicYear(),
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Organization')
                            ->findOneById($formData['organization'])
                    );
                }

                $tshirts = unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.tshirt_article')
                );

                if (null !== $metaData) {
                    if (null !== $metaData->getTshirtSize()) {
                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Booking')
                            ->findOneAssignedByArticleAndPerson(
                                $this->getEntityManager()
                                    ->getRepository('CudiBundle\Entity\Sale\Article')
                                    ->findOneById($tshirts[$metaData->getTshirtSize()]),
                                $academic
                            );

                        if (null !== $booking)
                            $this->getEntityManager()->remove($booking);
                    }

                    $becomeMember = $metaData->becomeMember() ? true : $formData['become_member'];

                    if ($becomeMember) {
                        $metaData->setBecomeMember($becomeMember)
                            ->setReceiveIrReeelAtCudi($formData['irreeel'])
                            ->setBakskeByMail($formData['bakske'])
                            ->setTshirtSize($formData['tshirt_size']);
                    } else {
                        $metaData->setBakskeByMail($formData['bakske']);
                    }
                } else {
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
                    $this->_bookRegistrationArticles($academic, $formData['tshirt_size'], $this->getCurrentAcademicYear());
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
                        'Your registration was succesfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'secretary_registration',
                    array(
                        'action' => 'studies',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'termsAndConditions' => $termsAndConditions,
                'studentDomain' => $studentDomain,
            )
        );
    }

    public function studiesAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        return $this->_studiesAction(
            $academic,
            $this->getCurrentAcademicYear()
        );
    }

    public function saveStudiesAction()
    {
        $this->initAjax();

        if (!($academic = $this->_getAcademic())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        return $this->_saveStudiesAction(
            $academic,
            $this->getCurrentAcademicYear(),
            $this->getRequest()->getPost()
        );
    }

    public function subjectsAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        return $this->_subjectAction(
            $academic,
            $this->getCurrentAcademicYear(),
            new SubjectForm()
        );
    }

    public function saveSubjectsAction()
    {
        $this->initAjax();

        if (!($academic = $this->_getAcademic())) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        return $this->_saveSubjectAction(
            $academic,
            $this->getCurrentAcademicYear(),
            $this->getRequest()->getPost()
        );
    }

    public function completeAction()
    {
        if (!($academic = $this->_getAcademic())) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

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
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $subjectIds = array();
        foreach($subjects as $enrollment)
            $subjectIds[] = $enrollment->getSubject()->getId();

        return new ViewModel(
            array(
                'academicYear' => $this->getCurrentAcademicYear(),
                'academic' => $academic,
                'metaData' => $metaData,
                'studies' => $mappings,
                'subjects' => $subjectIds,
            )
        );
    }

    private function _getAcademic()
    {
        return $this->getAuthentication()->getPersonObject();
    }

    private function _isValidCode()
    {
        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        if (null !== $code)
            return true;

        return false;
    }

    private function _getRegisterhibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if ('%2F' != substr($shibbolethUrl, 0, -3))
            $shibbolethUrl .= '%2F';

        return $shibbolethUrl . '?source=register';
    }

    private function _authenticate()
    {
        $authentication = new Authentication(
            new ShibbolethAdapter(
                $this->getEntityManager(),
                'CommonBundle\Entity\User\Person\Academic',
                'universityIdentification'
            ),
            $this->getServiceLocator()->get('authentication_doctrineservice')
        );

        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        $this->getEntityManager()->remove($code);
        $this->getEntityManager()->flush();

        $authentication->authenticate(
            $this->getParam('identification'), '', true
        );
    }
}