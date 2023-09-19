<?php

namespace SecretaryBundle\Controller;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter;
use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\PreferenceMapping;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use MailBundle\Component\Api\SibApi\SibApiHelper;
use MailBundle\Component\Api\SibApi\SibApiHelperResponse;
use SecretaryBundle\Entity\Registration;

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
     * @param  MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $enableRegistration = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        if (!$enableRegistration) {
            return $this->notFoundAction();
        }

        return $result;
    }

    public function addAction()
    {
        if ($this->getAuthentication()->isAuthenticated()) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'edit',
                )
            );
        }

        if ($this->getParam('identification') !== null) {
            if (substr($this->getParam('identification'), 0, 1) == 'u') {
                $this->flashMessenger()->warn(
                    'WARNING',
                    'As a professor, you do not have to register. An account has already been created automatically for you.'
                );

                $this->redirect()->toRoute(
                    'common_index',
                    array(
                        'action' => 'index',
                    )
                );

                return new ViewModel();
            }

            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($this->getParam('identification'));

            if ($academic !== null && $academic->getOrganizationStatus($this->getCurrentAcademicYear()) !== null) {
                $this->flashMessenger()->warn(
                    'WARNING',
                    'You have already registered for this academic year.'
                );

                if ($this->isValidCode()) {
                    $authentication = new Authentication(
                        new ShibbolethAdapter(
                            $this->getEntityManager(),
                            'CommonBundle\Entity\User\Person\Academic',
                            'universityIdentification'
                        ),
                        $this->getAuthenticationService()
                    );
                    $authentication->authenticate(
                        $this->getParam('identification'),
                        '',
                        true
                    );
                }

                $this->redirect()->toRoute(
                    'secretary_registration',
                    array(
                        'action' => 'studies',
                    )
                );

                return new ViewModel();
            }
        } else {
            $academic = null;
        }

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $enableOtherOrganization = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_other_organization');

        $termsAndConditions = $this->getTermsAndConditions();

        if ($academic !== null) {
            $this->authenticate();

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

        $ids = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $isicMembership = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.isic_membership') == 1;
        $isicRedirect = false;

        $membershipArticles = array();
        foreach ($ids as $organization => $id) {
            $membershipArticles[$organization] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);
        }

        if ($this->getRequest()->isPost()) {
            if ($this->isValidCode()) {
                $code = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                    ->findLastByUniversityIdentification($this->getParam('identification'));

                $form = $this->getForm(
                    'secretary_registration_add',
                    array(
                        'identification' => $this->getParam('identification'),
                        'extra_info'     => $code !== null ? unserialize($code->getInfo()) : array(),
                    )
                );

                $formData = $this->getRequest()->getPost()->toArray();
                $formData['academic']['university_identification'] = $this->getParam('identification');

                $form->setData($formData);

                if (isset($formData['organization_info']['organization'])) {
                    if ($formData['organization_info']['organization'] == 0 && $enableOtherOrganization) {
                        $selectedOrganization = null;
                    } else {
                        $selectedOrganization = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Organization')
                            ->findOneById($formData['organization_info']['organization']);
                    }
                } else {
                    $selectedOrganization = current(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Organization')
                            ->findAll()
                    );
                }

                if ($form->isValid()) {
                    $formData = $form->getData();
                    $metaData = $form->hydrateObject();
                    $academic = $metaData->getAcademic();

                    $this->getEntityManager()->persist($academic);
                    $this->getEntityManager()->persist($metaData);

                    $organizationData = $formData['organization_info'];

                    if (isset($organizationData['organization']) && $selectedOrganization) {
                        $this->setOrganization(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $selectedOrganization
                        );
                    }

                    if ($academic->canHaveOrganizationStatus($this->getCurrentAcademicYear())) {
                        $academic->addOrganizationStatus(
                            new OrganizationStatus(
                                $academic,
                                'non_member',
                                $this->getCurrentAcademicYear()
                            )
                        );
                    }

                    if ($organizationData['become_member']) {
                        if ($selectedOrganization) {
                            if ($isicMembership) {
                                $isicRedirect = true;
                            } else {
                                $this->bookRegistrationArticles($academic, $organizationData['tshirt_size'], $selectedOrganization, $this->getCurrentAcademicYear());
                            }
                        }
                    }

                    $noMail = $formData['academic']['no_mail'];
                    if ($noMail) {
                        $univMail = $formData['academic']['university']['email'] . '@student.kuleuven.be';
                        $personalMail = $formData['academic']['personal_email'];
                        $this->addToExcluded($univMail);
                        $this->addToExcluded($personalMail);
                    }

                    $academic->activate(
                        $this->getEntityManager(),
                        $this->getMailTransport()
                    );

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
                        $this->getAuthenticationService()
                    );

                    if ($code !== null) {
                        $this->getEntityManager()->remove($code);
                    }
                    $this->getEntityManager()->flush();

                    $authentication->authenticate(
                        $this->getParam('identification'),
                        '',
                        true
                    );

                    if ($isicRedirect) {
                        $this->redirect()->toRoute(
                            'cudi_isic',
                            array(
                                'action'       => 'form',
                                'redirect'     => 'secretary_registration',
                                'rediraction'  => 'studies',
                                'organization' => $selectedOrganization->getId(),
                                'size'         => $organizationData['tshirt_size'],
                            )
                        );
                    } else {
                        $this->flashMessenger()->success(
                            'SUCCESS',
                            'You are succesfully registered!'
                        );

                        $this->redirect()->toRoute(
                            'secretary_registration',
                            array(
                                'action' => 'studies',
                            )
                        );
                    }

                    return new ViewModel();
                }

                return new ViewModel(
                    array(
                        'form'                    => $form,
                        'termsAndConditions'      => $termsAndConditions,
                        'studentDomain'           => $studentDomain,
                        'organizations'           => $organizations,
                        'membershipArticles'      => $membershipArticles,
                        'selectedOrganization'    => $selectedOrganization,
                        'isPost'                  => true,
                        'enableOtherOrganization' => $enableOtherOrganization,
                        'academicYear'            => $this->getCurrentAcademicYear(),
                    )
                );
            }
        } else {
            if ($this->isValidCode()) {
                $code = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                    ->findLastByUniversityIdentification($this->getParam('identification'));

                $form = $this->getForm(
                    'secretary_registration_add',
                    array(
                        'identification' => $this->getParam('identification'),
                        'extra_info'     => $code !== null ? unserialize($code->getInfo()) : array(),
                    )
                );

                return new ViewModel(
                    array(
                        'form'                    => $form,
                        'termsAndConditions'      => $termsAndConditions,
                        'studentDomain'           => $studentDomain,
                        'organizations'           => $organizations,
                        'membershipArticles'      => $membershipArticles,
                        'enableOtherOrganization' => $enableOtherOrganization,
                        'academicYear'            => $this->getCurrentAcademicYear(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'registerShibbolethUrl' => $this->getRegisterShibbolethUrl(),
            )
        );
    }

    public function editAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        $enableRegistration = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $enableOtherOrganization = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_other_organization');

        $termsAndConditions = $this->getTermsAndConditions();

        if ($metaData !== null) {
            $form = $this->getForm('secretary_registration_edit', array('meta_data' => $metaData));
        } else {
            $form = $this->getForm('secretary_registration_edit', array('academic' => $academic));
        }

        $ids = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $isicMembership = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.isic_membership') == 1;
        $isicRedirect = false;
        $isicOrders = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\IsicCard')
            ->findByPersonAndYearQuery($academic, $this->getCurrentAcademicYear())
            ->getResult();

        $membershipArticles = array();
        foreach ($ids as $organization => $id) {
            $membershipArticles[$organization] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);
        }

        $tshirts = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.tshirt_article')
        );

        $oldTshirtBooking = null;
        $oldTshirtSize = null;
        if ($metaData !== null) {
            if ($enableRegistration) {
                if ($metaData->getTshirtSize() !== null && array_key_exists($metaData->getTshirtSize(), $tshirts)) {
                    $oldTshirtBooking = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Booking')
                        ->findOneAssignedByArticleAndPersonInAcademicYear(
                            $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Article')
                                ->findOneById($tshirts[$metaData->getTshirtSize()]),
                            $academic,
                            $this->getCurrentAcademicYear()
                        );
                }
            }
            $oldTshirtSize = $metaData->getTshirtSize();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost()->toArray();
            $formData['academic']['university_identification'] = $academic->getUniversityIdentification();

            if ($metaData && $metaData->becomeMember()) {
                $formData['organization_info']['become_member'] = true;
            } else {
                $formData['organization_info']['become_member'] = $formData['organization_info']['become_member'] ?? 0;
            }
            $formData['organization_info']['conditions'] = true;

            $organizationData = $formData['organization_info'];

            if (isset($organizationData['organization'])) {
                if ($organizationData['organization'] == 0 && $enableOtherOrganization) {
                    $selectedOrganization = null;
                } else {
                    $selectedOrganization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOneById($organizationData['organization']);
                }
            } else {
                $selectedOrganization = current(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findAll()
                );
            }

            $form->setData($formData);

            if ($form->isValid()) {
                if ($metaData === null) {
                    $metaData = $form->hydrateObject();

                    $this->getEntityManager()->persist($metaData);
                }

                if ($academic->canHaveOrganizationStatus($this->getCurrentAcademicYear())) {
                    $academic->addOrganizationStatus(
                        new OrganizationStatus(
                            $academic,
                            'non_member',
                            $this->getCurrentAcademicYear()
                        )
                    );
                }

                if ($selectedOrganization !== null) {
                    $this->setOrganization(
                        $academic,
                        $this->getCurrentAcademicYear(),
                        $selectedOrganization
                    );
                }

                if ($enableRegistration) {
                    if ($oldTshirtBooking !== null && $oldTshirtSize != $metaData->getTshirtSize()) {
                        $this->getEntityManager()->remove($oldTshirtBooking);
                    }

                    $membershipArticles = array();
                    $ids = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('secretary.membership_article')
                    );

                    foreach ($ids as $organizationId => $articleId) {
                        $membershipArticles[$organizationId] = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($articleId);
                    }

                    if ($metaData->becomeMember() && $selectedOrganization !== null) {
                        if ($isicMembership && count($isicOrders) > 0) {
                            $isicRedirect = true;
                        } else {
                            $this->bookRegistrationArticles($academic, $organizationData['tshirt_size'], $selectedOrganization, $this->getCurrentAcademicYear());
                        }
                    } else {
                        foreach ($membershipArticles as $membershipArticle) {
                            $booking = $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Booking')
                                ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                                    $membershipArticle,
                                    $academic,
                                    $this->getCurrentAcademicYear()
                                );

                            if ($booking !== null) {
                                $removeBooking = true;
                                foreach ($isicOrders as $isicOrder) {
                                    if ($isicOrder->getBooking() == $booking) {
                                        $removeBooking = false;
                                    }
                                }

                                if ($removeBooking) {
                                    $this->getEntityManager()->remove($booking);
                                }
                            }
                        }
                    }
                }

                $noMail = $formData['academic']['no_mail'];
                if ($noMail) {
                    $univMail = $formData['academic']['university']['email'] . '@student.kuleuven.be';
                    $personalMail = $formData['academic']['personal_email'];
                    $this->addToExcluded($univMail);
                    $this->addToExcluded($personalMail);
                }

                $academic->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport()
                );

                $registration = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

                if ($registration === null) {
                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->getEntityManager()->persist($registration);
                }

                $this->getEntityManager()->flush();

                if ($isicRedirect) {
                    $this->redirect()->toRoute(
                        'cudi_isic',
                        array(
                            'action'       => 'form',
                            'redirect'     => 'secretary_registration',
                            'rediraction'  => 'studies',
                            'organization' => $selectedOrganization->getId(),
                            'size'         => $organizationData['tshirt_size'],
                        )
                    );
                } else {
                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'You are succesfully registered!'
                    );

                    $this->redirect()->toRoute(
                        'secretary_registration',
                        array(
                            'action' => 'studies',
                        )
                    );
                }

                return new ViewModel();
            } else {
                return new ViewModel(
                    array(
                        'form'                    => $form,
                        'termsAndConditions'      => $termsAndConditions,
                        'studentDomain'           => $studentDomain,
                        'membershipArticles'      => $membershipArticles,
                        'organizations'           => $organizations,
                        'selectedOrganization'    => $selectedOrganization,
                        'isPost'                  => true,
                        'enableOtherOrganization' => $enableOtherOrganization,
                        'academicYear'            => $this->getCurrentAcademicYear(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form'                    => $form,
                'termsAndConditions'      => $termsAndConditions,
                'studentDomain'           => $studentDomain,
                'membershipArticles'      => $membershipArticles,
                'organizations'           => $organizations,
                'enableOtherOrganization' => $enableOtherOrganization,
                'academicYear'            => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function studiesAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        return $this->doStudiesAction(
            $academic,
            $this->getCurrentAcademicYear()
        );
    }

    public function saveStudiesAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        return $this->doSaveStudiesAction(
            $academic,
            $this->getCurrentAcademicYear(),
            $this->getRequest()->getPost()->toArray()
        );
    }

    public function subjectsAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'secretary_registration',
                array(
                    'action' => 'add',
                )
            );

            return new ViewModel();
        }

        $form = $this->getForm('secretary_registration_subject_add');

        return $this->doSubjectAction(
            $academic,
            $this->getCurrentAcademicYear(),
            $form
        );
    }

    public function saveSubjectsAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        return $this->doSaveSubjectAction(
            $academic,
            $this->getCurrentAcademicYear(),
            $this->getRequest()->getPost()->toArray()
        );
    }

    public function preferencesAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $preferenceMappings = $academic->getPreferenceMappings();
        $preferences = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Preference')
            ->findAll();

        $this->syncPreferenceMappings($academic, $preferenceMappings, $preferences);

        return new ViewModel(
            array(
                'preferenceMappings' => $academic->getPreferenceMappings(),
            )
        );
    }

    public function savePreferencesAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $data = $this->getRequest()->getPost()->toArray();

        if (isset($data['preference_mappings_true'])) {
            foreach ($data['preference_mappings_true'] as $id) {
                $preferenceMapping = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\PreferenceMapping')
                    ->findOnebyId($id);
                $preferenceMapping->setValue(true);
                $this->getEntityManager()->persist($preferenceMapping);
                $this->getEntityManager()->flush();
            }
        }

        if (isset($data['preference_mappings_false'])) {
            foreach ($data['preference_mappings_false'] as $id) {
                $preferenceMapping = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\PreferenceMapping')
                    ->findOnebyId($id);
                $preferenceMapping->setValue(false);
                $this->getEntityManager()->persist($preferenceMapping);
                $this->getEntityManager()->flush();
            }
        }

        $this->getEntityManager()->persist($academic);
        $this->getEntityManager()->flush();

        $this->updateSibAttributes($academic);

        $this->redirect()->toRoute(
            'secretary_registration',
            array(
                'action' => 'preferences',
            )
        );
        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function completeAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $mappings = array();
        foreach ($studies as $enrollment) {
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects'   => $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
                    ->findAllByStudy($enrollment->getStudy()),
            );
        }

        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $subjectIds = array();
        foreach ($subjects as $enrollment) {
            $subjectIds[] = $enrollment->getSubject()->getId();
        }

        return new ViewModel(
            array(
                'academicYear' => $this->getCurrentAcademicYear(),
                'academic'     => $academic,
                'metaData'     => $metaData,
                'studies'      => $mappings,
                'subjects'     => $subjectIds,
            )
        );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        $person = $this->getAuthentication()->getPersonObject();

        if (!($person instanceof Academic)) {
            return;
        }

        return $person;
    }

    /**
     * @return boolean
     */
    private function isValidCode()
    {
        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        return $code !== null || getenv('APPLICATION_ENV') == 'development';
    }

    /**
     * @return string
     */
    private function getRegisterShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if (@unserialize($shibbolethUrl) !== false) {
            $shibbolethUrl = unserialize($shibbolethUrl);

            if (getenv('SERVED_BY') === false) {
                throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
            }
            if (!isset($shibbolethUrl[getenv('SERVED_BY')])) {
                throw new ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');
            }

            $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
        }

        return $shibbolethUrl . '?source=register';
    }

    /**
     * @return null
     */
    private function authenticate()
    {
        $authentication = new Authentication(
            new ShibbolethAdapter(
                $this->getEntityManager(),
                'CommonBundle\Entity\User\Person\Academic',
                'universityIdentification'
            ),
            $this->getAuthenticationService()
        );

        $code = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
            ->findLastByUniversityIdentification($this->getParam('identification'));

        if ($code !== null) {
            $this->getEntityManager()->remove($code);
            $this->getEntityManager()->flush();

            $authentication->authenticate(
                $this->getParam('identification'),
                '',
                true
            );
        }
    }

    private function addToExcluded(string $email)
    {
        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $werkendGroups = array();

        foreach ($groups as $group) {
            if (strpos($group->getName(), '[werkend]')) {
                array_push($werkendGroups, $group);
            }
        }

        foreach ($werkendGroups as $werkend) {
            $werkend->addToExcluded($email);
        }
    }

    /**
     * Newly added sections in Litus admin are added to account preferences of academic with default value, and
     * removed sections in Litus admin are removed from account preferences of academic.
     *
     * @param Academic $academic
     * @param ArrayCollection $preferenceMappings
     * @param $sections
     * @return void
     */
    private function syncPreferenceMappings($academic, $preferenceMappings, $sections) {
        if ($sections != null) {
            foreach ($sections as $section) {
                // possible that new sections are added in admin that are not yet in academic's preferences -> add those with their default value
                if (!($section->inPreferencesMappings($preferenceMappings))) {
                    $prefToAdd = new PreferenceMapping($academic, $section, $section->getDefaultValue());
                    $this->getEntityManager()->persist($prefToAdd);
                    $this->getEntityManager()->flush();
                }
            }
        }

        if ($preferenceMappings != null ) {
            foreach ($preferenceMappings as $preferenceMapping) {
                // possible that sections are removed in admin that are still in academic's preferences -> remove those
                if (!($preferenceMapping->inPreferences($sections))) {
                    $academic->removePreferenceMapping($preferenceMapping);
                    $this->getEntityManager()->remove($preferenceMapping);
                    $this->getEntityManager()->flush();
                }
            }
        }

        $this->getEntityManager()->persist($academic);
        $this->getEntityManager()->flush();
    }

    /**
     * Updates all the SIB attributes to the current values of the Academic's preferences.
     *
     * @return sibApiHelperResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSibAttributes($academic)
    {
        $sibApiHelper = new SibApiHelper($this->getEntityManager());

        $email = $academic->getPersonalEmail();
        foreach ($academic->getPreferenceMappings() as $preferenceMapping) {
            $attributeName = $preferenceMapping->getPreference()->getAttribute();
            $value = $preferenceMapping->getValue();
            $sibApiHelperResponse = $sibApiHelper->createOrUpdateContact($email, $attributeName, $value);

            if (!$sibApiHelperResponse->success) {
                return $sibApiHelperResponse;
            }
        }

        return sibApiHelperResponse::successful();
    }
}
