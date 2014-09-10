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

namespace CommonBundle\Controller;

use CommonBundle\Component\PassKit\Pass\Membership;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Entity\User\Credential;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Booking;
use DateTime;
use Imagick;
use SecretaryBundle\Entity\Organization\MetaData;
use SecretaryBundle\Entity\Registration;
use Zend\Http\Headers;
use Zend\File\Transfer\Adapter\Http as FileUpload;
use Zend\InputFilter\InputInterface;
use Zend\View\Model\ViewModel;

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
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
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
            ->findAllByAcademicAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());

        $subjectIds = array();
        foreach($subjects as $enrollment)
            $subjectIds[] = $enrollment->getSubject()->getId();

        $profileForm = $this->getForm('common_account_profile');
        $profileForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'common_account',
                array(
                    'action' => 'uploadProfileImage',
                )
            )
        );

        return new ViewModel(
            array(
                'academicYear' => $this->getCurrentAcademicYear(),
                'metaData' => $metaData,
                'studies' => $mappings,
                'subjects' => $subjectIds,
                'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'profileForm' => $profileForm,
            )
        );
    }

    public function editAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        $enableRegistration = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        $academic = $this->getAuthentication()->getPersonObject();

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        $termsAndConditions = $this->_getTermsAndConditions();

        $enableOtherOrganization = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_other_organization');

        if (null !== $metaData) {
            $form = $this->getForm('common_account_edit', array(
                'meta_data' => $metaData,
            ));
        } else {
            $form = $this->getForm('common_account_edit', array(
                'academic' => $academic,
            ));
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

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost()->toArray();

            $formData['academic']['university_identification'] = $academic->getUniversityIdentification();
            if ($metaData && $metaData->becomeMember())
                $formData['organization_info']['become_member'] = true;
            elseif (!isset($formData['organization_info']['become_member']))
                $formData['organization_info']['become_member'] = false;

            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                if (null === $metaData) {
                    $metaData = $form->hydrateObject();

                    $this->getEntityManager()->persist($metaData);
                }

                if (isset($formData['organization_info']['organization'])) {
                    if (0 == $formData['organization_info']['organization'] && $enableOtherOrganization) {
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

                if ($enableRegistration) {
                    if (null !== $metaData->getTshirtSize()) {
                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Booking')
                            ->findOneAssignedByArticleAndPersonInAcademicYear(
                                $this->getEntityManager()
                                    ->getRepository('CudiBundle\Entity\Sale\Article')
                                    ->findOneById($tshirts[$metaData->getTshirtSize()]),
                                $academic,
                                $this->getCurrentAcademicYear()
                            );

                        if ($booking !== null)
                            $this->getEntityManager()->remove($booking);
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

                    if ($metaData->becomeMember()) {
                        $this->_bookRegistrationArticles($academic, $formData['tshirt_size'], $organization, $this->getCurrentAcademicYear());
                    } else {
                        foreach ($membershipArticles as $membershipArticle) {
                            $booking = $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Booking')
                                ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                                    $membershipArticle,
                                    $academic,
                                    $this->getCurrentAcademicYear()
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

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'Your data was succesfully updated!'
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
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
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
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
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
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return new ViewModel();
        }

        return $this->_subjectAction(
            $this->getAuthentication()->getPersonObject(),
            $this->getCurrentAcademicYear(),
            $this->getForm('secretary_registration_subject_add')
        );
    }

    public function saveSubjectsAction()
    {
        if (null === $this->getAuthentication()->getPersonObject()) {
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
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

        $form = $this->getForm('common_account_activate');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $user->setCode(null)
                    ->setCredential(
                        new Credential(
                            $formData['credential']
                        )
                    );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'Your account was succesfully activated!'
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
                'data' => $pass->getContent(),
            )
        );
    }

    public function uploadProfileImageAction()
    {
        $form = $this->getForm('common_account_profile');

        $upload = new FileUpload();
        $inputFilter = $form->getInputFilter()->get('profile');
        if ($inputFilter instanceof InputInterface)
            $upload->setValidators($inputFilter->getValidatorChain()->getValidators());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $academic = $this->getAuthentication()->getPersonObject();
            $filePath = 'public'.$this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.profile_path');

            if ($form->isValid()) {
                if ($upload->isValid()) {
                    $upload->receive();

                    $image = new Imagick($upload->getFileName('profile'));
                    unlink($upload->getFileName('profile'));
                } else {
                    $image = new Imagick($filePath.'/'.$academic->getPhotoPath());
                }

                if ($formData['x'] == 0 && $formData['y'] == 0 && $formData['x2'] == 0 && $formData['y2'] == 0 && $formData['w'] == 0 && $formData['h'] == 0) {
                    $image->cropThumbnailImage(320, 240);
                } else {
                    $ratio = $image->getImageWidth()/320;
                    $x = $formData['x'] * $ratio;
                    $y = $formData['y'] * $ratio;
                    $w = $formData['w'] * $ratio;
                    $h = $formData['h'] * $ratio;

                    $image->cropImage($w, $h, $x, $y);
                    $image->cropThumbnailImage(320, 240);
                }

                if ($academic->getPhotoPath() != '' || $academic->getPhotoPath() !== null) {
                    $fileName = $academic->getPhotoPath();
                } else {
                    do {
                        $fileName = sha1(uniqid());
                    } while (file_exists($filePath.'/'.$fileName));
                }
                $image->writeImage($filePath.'/'.$fileName);
                $academic->setPhotoPath($fileName);

                $this->getEntityManager()->flush();

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'success',
                            'profile' => $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('common.profile_path').'/'.$fileName,
                        ),
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $key => $element) {
                    if (!isset($errors[$element->getName()]))
                        continue;

                    $formErrors[$element->getAttribute('id')] = array();

                    foreach ($errors[$element->getName()] as $error) {
                        $formErrors[$element->getAttribute('id')][] = $error;
                    }
                }

                if (sizeof($upload->getMessages()) > 0)
                    $formErrors['profile'] = $upload->getMessages();

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'form' => array(
                                'errors' => $formErrors,
                            ),
                        ),
                    )
                );
            }
        }
    }

    /**
     * @return Person|null
     */
    private function _getUser()
    {
        if (null === $this->getParam('code')) {
            $this->flashMessenger()->error(
                'Error',
                'No code was given to identify the user!'
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
            $this->flashMessenger()->error(
                'Error',
                'The given code is not valid!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return;
        }

        return $user;
    }

    /**
     * @return null
     */
    private function _doRedirect()
    {
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

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date.' 00:00') ?: null;
    }
}
