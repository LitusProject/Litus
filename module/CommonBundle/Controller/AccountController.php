<?php

namespace CommonBundle\Controller;

use CommonBundle\Entity\User\Credential;
use CommonBundle\Entity\User\Person;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\PreferenceMapping;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use CudiBundle\Form\Admin\Sale\Article\View;
use Doctrine\Common\Collections\ArrayCollection;
use Imagick;
use Laminas\View\Model\ViewModel;
use SecretaryBundle\Entity\Registration;

/**
 * Handles account page.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AccountController extends \SecretaryBundle\Component\Controller\RegistrationController
{
    public function indexAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        // Cudi (All assigned bookings)
        $allBookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($academic);
        $bookings = array();
        $futureBookings = array();
        foreach ($allBookings as $booking) {
            if ($booking->getStatus() == 'assigned') {
                array_push($bookings, $booking);
            } else {
                array_push($futureBookings, $booking);
            }
        }

        $total = 0;
        foreach ($bookings as $booking) {
            $total += $booking->getArticle()->getSellPrice() * $booking->getNumber();
        }

        // Shifts
        $myShifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($academic);

        // Timeslots
        $mySlots = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\RegistrationShift')
            ->findAllActiveByPerson($academic);

        // Reservations
        $reservations = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation')
            ->getAllCurrentReservationsByPerson($academic);

        //Consumptions
        if ($this->getEntityManager()->getRepository('TicketBundle\Entity\Consumptions')->findOneByPerson($academic) != null
        ) {
            $consumptions = $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Consumptions')
                ->findOneByPerson($academic)->getConsumptions();
        } else {
            $consumptions = 0;
        }

        return new ViewModel(
            array(
                'academicYear'     => $this->getCurrentAcademicYear(),
                'entityManager'    => $this->getEntityManager(),
                'organizationYear' => $this->getCurrentAcademicYear(true),
                'bookings'         => $bookings,
                'futureBookings'   => $futureBookings,
                'total'            => $total,
                'shifts'           => $myShifts,
                'timeslots'        => $mySlots,
                'reservations'     => $reservations,
                'shopName'         => $this->getShopName(),
                'consumptions'     => $consumptions,
            )
        );
    }

    public function profileAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

        // Retrieve the studies and its subjects
        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

//        $allStudies = array();
//        $allSubjects = array();
//        $subjectIds = array();  // To avoid duplicates
//        foreach ($studies as $study) {
//            $subjects = $this->getEntityManager()
//                ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
//                ->findAllByStudy($study->getStudy());
//            $allStudies[] = $study->getStudy();
//            foreach ($subjects as $subject) {
//                if (!in_array($subject->getSubject()->getId(), $subjectIds)) {
//                    $subjectIds[] = $subject->getSubject()->getId();
//                    $allSubjects[] = $subject->getSubject();
//                }
//            }
//        }

        // Retrieve the other subjects
        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
            ->findAllByAcademicAndAcademicYear($academic, $this->getCurrentAcademicYear());

//        foreach ($subjects as $subject) {
//            if (!in_array($subject->getSubject()->getId(), $subjectIds)) {
//                $subjectIds[] = $subject->getSubject()->getId();
//                $allSubjects[] = $subject->getSubject();
//            }
//        }

        $preferenceMappings = $academic->getPreferenceMappings();
        $sections = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Preference')
            ->findAll();

        $this->syncPreferenceMappings($academic, $preferenceMappings, $sections);

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

        $signatureEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('enable_organization_signature');

        return new ViewModel(
            array(
                'academicYear'          => $this->getCurrentAcademicYear(),
                'organizationYear'      => $this->getCurrentAcademicYear(true),
                'signatureEnabled'      => $signatureEnabled,
                'metaData'              => $metaData,
                'studies'               => $studies,
                'subjects'              => $subjects,
                'profilePath'           => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'profileForm'           => $profileForm,
                'preferenceMappings'    => $academic->getPreferenceMappings(),
            )
        );
    }

    public function editAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
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

        $termsAndConditions = $this->getTermsAndConditions();

        if ($metaData !== null) {
            $form = $this->getForm(
                'common_account_edit',
                array(
                    'meta_data' => $metaData,
                )

            );
        } else {
            $form = $this->getForm(
                'common_account_edit',
                array(
                    'academic' => $academic,
                )
            );
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
        $isicOrder = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\IsicCard')
            ->findByPersonAndYearQuery($academic, $this->getCurrentAcademicYear())
            ->getResult();
        if (count($isicOrder) == 0) {
            $isicOrder = null;
        } else {
            $isicOrder = $isicOrder[0];
        }

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
                        if ($isicMembership && $isicOrder == null) {
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
                            if ($booking !== null && $isicOrder !== null && $isicOrder->getBooking() !== $booking) {
                                $this->getEntityManager()->remove($booking);
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
                            'redirect'     => $this->getParam('return') ? $this->getParam('return') : 'common_account',
                            'organization' => $selectedOrganization->getId(),
                            'size'         => $organizationData['tshirt_size'],
                        )
                    );
                } else {
                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'Your data was succesfully updated!'
                    );

                    $this->doRedirect();
                }

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'               => $form,
                'metaData'           => $metaData,
                'membershipArticles' => $membershipArticles,
                'termsAndConditions' => $termsAndConditions,
                'studentDomain'      => $studentDomain,
                'academicYear'       => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function studiesAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        return $this->doStudiesAction(
            $academic,
            $this->getCurrentAcademicYear()
        );
    }

    public function saveStudiesAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $this->initAjax();

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
            return new ViewModel();
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
        $sections = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Preference')
            ->findAll();

        $this->syncPreferenceMappings($academic, $preferenceMappings, $sections);

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

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function activateAction()
    {
        $user = $this->getPersonEntity();
        if ($user === null) {
            return new ViewModel();
        }

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

    public function uploadProfileImageAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $form = $this->getForm('common_account_profile');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            $filePath = 'public' . $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.profile_path');

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($formData['profile']) {
                    $image = new Imagick($formData['profile']['tmp_name']);
                } else {
                    $image = new Imagick($filePath . '/' . $academic->getPhotoPath());
                }

                if ($formData['x'] == 0 && $formData['y'] == 0 && $formData['x2'] == 0 && $formData['y2'] == 0 && $formData['w'] == 0 && $formData['h'] == 0) {
                    $image->cropThumbnailImage(320, 240);
                } else {
                    $ratio = $image->getImageWidth() / 320;
                    $x = $formData['x'] * $ratio;
                    $y = $formData['y'] * $ratio;
                    $w = $formData['w'] * $ratio;
                    $h = $formData['h'] * $ratio;

                    $image->cropImage($w, $h, $x, $y);
                    $image->cropThumbnailImage(320, 240);
                }

                do {
                    $newFileName = sha1(uniqid());
                } while (file_exists($filePath . '/' . $newFileName));

                if ($academic->getPhotoPath() != '' || $academic->getPhotoPath() !== null) {
                    $fileName = $academic->getPhotoPath();

                    if (file_exists($filePath . '/' . $fileName)) {
                        unlink($filePath . '/' . $fileName);
                    }
                }

                $image->writeImage($filePath . '/' . $newFileName);
                $academic->setPhotoPath($newFileName);

                $this->getEntityManager()->flush();

                return new ViewModel(
                    array(
                        'result' => array(
                            'status'  => 'success',
                            'profile' => $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('common.profile_path') . '/' . $newFileName,
                        ),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'form'   => array(
                                'errors' => $form->getMessages(),
                            ),
                        ),
                    )
                );
            }
        }
    }

    /**
     * @return string
     */
    private function getName($item)
    {
        return $item->getName();
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            $this->flashMessenger()->error(
                'Error',
                'Please login first!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return;
        }

        return $academic;
    }

    /**
     * @return Person|null
     */
    private function getPersonEntity()
    {
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Code')
            ->findOnePersonByCode($this->getParam('code'));

        if (!($person instanceof Person)) {
            $this->flashMessenger()->error(
                'Error',
                'No person was found!'
            );

            $this->redirect()->toRoute(
                'common_index'
            );

            return;
        }

        return $person;
    }

    /**
     * @return null
     */
    private function doRedirect()
    {
        if ($this->getParam('return') === null) {
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
     * @return string
     */
    private function getShopName()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shop.name');
    }

    private function addToExcluded(string $email)
    {
        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $werkendGroups = array();
        foreach ($groups as $group) {
            if (strpos($group->getName(), 'werkend')) {
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
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSibAttributes($academic)
    {
        $api = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.sib_api');
        $client = new \GuzzleHttp\Client();
        $email = $academic->getPersonalEmail();
        foreach ($academic->getPreferenceMappings() as $preferenceMapping) {
            $name = $preferenceMapping->getPreference()->getAttribute();
            $value = $preferenceMapping->getValue()? "true" : "false";
            $client->request('PUT', 'https://api.sendinblue.com/v3/contacts/'.$email, [
                'body' => '{"attributes":{"'.$name.'":'.$value.'}}',
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'api-key' => $api,
                ],
            ]);
        }
    }
}
