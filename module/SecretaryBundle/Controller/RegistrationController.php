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

namespace SecretaryBundle\Controller;

use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter;
use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use DateTime;
use SecretaryBundle\Entity\Organization\MetaData;
use SecretaryBundle\Entity\Registration;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

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
     * @param  MvcEvent                                                          $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $enableRegistration = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        if (!$enableRegistration)
            return $this->notFoundAction();

        return $result;
    }

    public function addAction()
    {
        if (null !== $this->getParam('identification')) {
            if ('u' == substr($this->getParam('identification'), 0, 1)) {
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

            if (null !== $academic && null !== $academic->getOrganizationStatus($this->getCurrentAcademicYear())) {
                $this->flashMessenger()->warn(
                    'WARNING',
                    'You have already registered for this academic year.'
                );

                if ($this->_isValidCode()) {
                    $authentication = new Authentication(
                        new ShibbolethAdapter(
                            $this->getEntityManager(),
                            'CommonBundle\Entity\User\Person\Academic',
                            'universityIdentification'
                        ),
                        $this->getAuthenticationService()
                    );
                    $authentication->authenticate(
                        $this->getParam('identification'), '', true
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

        $ids = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $membershipArticles = array();
        foreach ($ids as $organization => $id) {
            $membershipArticles[$organization] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);
        }

        if ($this->getRequest()->isPost()) {
            if ($this->_isValidCode()) {
                $code = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                    ->findLastByUniversityIdentification($this->getParam('identification'));

                $form = $this->getForm('secretary_registration_add', array(
                    'identification' => $this->getParam('identification'),
                    'extra_info'     => unserialize($code->getInfo()),
                ));

                $formData = $this->getRequest()->getPost()->toArray();
                $formData['academic']['university_identification'] = $this->getParam('identification');

                $form->setData($formData);

                if (isset($formData['organization_info']['organization'])) {
                    if (0 == $formData['organization_info']['organization'] && $enableOtherOrganization) {
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
                        $this->_setOrganization(
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
                        $this->_bookRegistrationArticles($academic, $organizationData['tshirt_size'], $selectedOrganization, $this->getCurrentAcademicYear());
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

                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();

                    $authentication->authenticate(
                        $this->getParam('identification'), '', true
                    );

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

                    return new ViewModel();
                }

                return new ViewModel(
                    array(
                        'form' => $form,
                        'termsAndConditions' => $termsAndConditions,
                        'studentDomain' => $studentDomain,
                        'organizations' => $organizations,
                        'membershipArticles' => $membershipArticles,
                        'selectedOrganization' => $selectedOrganization,
                        'isPost' => true,
                        'enableOtherOrganization' => $enableOtherOrganization,
                    )
                );
            }
        } else {
            if ($this->_isValidCode()) {
                $code = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                    ->findLastByUniversityIdentification($this->getParam('identification'));

                $form = $this->getForm('secretary_registration_add', array(
                    'identification' => $this->getParam('identification'),
                    // 'extra_info'     => unserialize($code->getInfo()),
                ));

                return new ViewModel(
                    array(
                        'form' => $form,
                        'termsAndConditions' => $termsAndConditions,
                        'studentDomain' => $studentDomain,
                        'organizations' => $organizations,
                        'membershipArticles' => $membershipArticles,
                        'enableOtherOrganization' => $enableOtherOrganization,
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

        $enableRegistration = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $enableOtherOrganization = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_other_organization');

        $termsAndConditions = $this->_getTermsAndConditions();

        if (null !== $metaData) {
            $form = $this->getForm('secretary_registration_edit', array('meta_data' => $metaData));
        } else {
            $form = $this->getForm('secretary_registration_edit', array('academic' => $academic));
        }

        $ids = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $membershipArticles = array();
        foreach ($ids as $organization => $id) {
            $membershipArticles[$organization] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);
        }

        $oldTshirtBooking = null;
        if (null !== $metaData) {
            if ($enableRegistration) {
                if (null !== $metaData->getTshirtSize()) {
                    // $oldTshirtBooking = $this->getEntityManager()
                    //     ->getRepository('CudiBundle\Entity\Sale\Booking')
                    //     ->findOneAssignedByArticleAndPersonInAcademicYear(
                    //         $this->getEntityManager()
                    //             ->getRepository('CudiBundle\Entity\Sale\Article')
                    //             ->findOneById($tshirts[$metaData->getTshirtSize()]),
                    //         $academic,
                    //         $this->getCurrentAcademicYear()
                    //     );
                }
            }
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost()->toArray();
            $formData['academic']['university_identification'] = $academic->getUniversityIdentification();

            if ($metaData && $metaData->becomeMember())
                $formData['organization_info']['become_member'] = true;
            else
                $formData['organization_info']['become_member'] = isset($formData['organization_info']['become_member'])
                    ? $formData['organization_info']['become_member']
                    : false;

            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                if (null === $metaData) {
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

                if (isset($formData['organization'])) {
                    if (0 == $formData['organization'] && $enableOtherOrganization) {
                        $organization = null;
                    } else {
                        $organization = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Organization')
                            ->findOneById($formData['organization_info']['organization']);

                        $this->_setOrganization(
                            $academic,
                            $this->getCurrentAcademicYear(),
                            $organization
                        );
                    }
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

                if (null !== $oldTshirtBooking) {
                    $this->getEntityManager()->remove($oldTshirtBooking);
                }

                if ($enableRegistration) {
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

                    if ($metaData->becomeMember()) {
                        $this->_bookRegistrationArticles($academic, $formData['tshirt_size'], $organization, $this->getCurrentAcademicYear());
                    } else {
                        // foreach ($membershipArticles as $membershipArticle) {
                        //     $booking = $this->getEntityManager()
                        //         ->getRepository('CudiBundle\Entity\Sale\Booking')
                        //         ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                        //             $membershipArticle,
                        //             $academic,
                        //             $this->getCurrentAcademicYear()
                        //         );
                        //
                        //     if (null !== $booking)
                        //         $this->getEntityManager()->remove($booking);
                        // }
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

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'Your registration was succesfully updated!'
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
                'membershipArticles' => $membershipArticles,
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
            $this->getForm('secretary_registration_subject_add')
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
        foreach ($studies as $enrollment) {
            $mappings[] = array(
                'enrollment' => $enrollment,
                'subjects' => $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByStudyAndAcademicYear($enrollment->getStudy(), $this->getCurrentAcademicYear()),
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
        // $code = $this->getEntityManager()
        //     ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
        //     ->findLastByUniversityIdentification($this->getParam('identification'));
        //
        // if (null !== $code)
            return true;

        // return false;
    }

    private function _getRegisterhibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        try {
            if (false !== ($shibbolethUrl = unserialize($shibbolethUrl))) {
                if (false === getenv('SERVED_BY'))
                    throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
                if (!isset($shibbolethUrl[getenv('SERVED_BY')]))
                    throw new ShibbolethUrlException('Array key '.getenv('SERVED_BY').' does not exist');

                $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
            }
        } catch (\ErrorException $e) {
            // No load balancer active
        }

        if ('%2F' != substr($shibbolethUrl, 0, -3))
            $shibbolethUrl .= '%2F';

        return $shibbolethUrl.'?source=register';
    }

    private function _authenticate()
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

        if (null !== $code) {
            $this->getEntityManager()->remove($code);
            $this->getEntityManager()->flush();

            $authentication->authenticate(
                $this->getParam('identification'), '', true
            );
        }
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date.' 00:00') ?: null;
    }
}
