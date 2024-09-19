<?php

namespace FormBundle\Controller;

use DateTime;
use FormBundle\Component\Form\Mail as MailHelper;
use FormBundle\Entity\Node\Entry as FormEntry;
use FormBundle\Entity\Node\Form;
use FormBundle\Entity\Node\Group;
use FormBundle\Entity\Node\GuestInfo;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * FormController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FormController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $formSpecification = $this->getFormEntity();
        if ($formSpecification === null) {
            return $this->notFoundAction();
        }

        if ($formSpecification->getType() == 'doodle') {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action' => 'doodle',
                    'id'     => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $now = new DateTime();
        if ($now < $formSpecification->getStartDate() || $now > $formSpecification->getEndDate() || !$formSpecification->isActive()) {
            return new ViewModel(
                array(
                    'message'       => 'This form is currently closed.',
                    'specification' => $formSpecification,
                )
            );
        }

        $person = null;
        $guestInfo = null;
        $entries = array();
        $draftVersion = null;

        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
            $entries = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findAllByFormAndPerson($formSpecification, $person);
            $draftVersion = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findDraftVersionByFormAndPerson($formSpecification, $person);
        } elseif ($this->isCookieSet()) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($this->getCookie());

            if ($guestInfo) {
                $entries = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findAllByFormAndGuestInfo($formSpecification, $guestInfo);
                $draftVersion = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findDraftVersionByFormAndGuestInfo($formSpecification, $guestInfo);
            }
        }

        $group = $this->getGroupEntity($formSpecification);
        $progressBarInfo = null;

        if ($group) {
            $progressBarInfo = $this->progressBarInfo($group, $formSpecification);

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                $this->flashMessenger()->warn(
                    'Warning',
                    'Please submit these forms in order.'
                );

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action' => 'index',
                        'id'     => $progressBarInfo['first_uncompleted_id'],
                    )
                );

                return new ViewModel();
            }
        }

        if ($person === null && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'message'       => 'Please login to view this form.',
                    'specification' => $formSpecification,
                )
            );
        } elseif (!$formSpecification->isMultiple() && count($entries) > 0 && !isset($draftVersion)) {
            return new ViewModel(
                array(
                    'message'         => 'You can\'t fill this form more than once.',
                    'specification'   => $formSpecification,
                    'entries'         => $entries,
                    'group'           => $group,
                    'progressBarInfo' => $progressBarInfo,
                )
            );
        }

        $entriesCount = count(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findAllByForm($formSpecification)
        );

        if ($formSpecification->getMax() != 0 && $entriesCount >= $formSpecification->getMax()) {
            return new ViewModel(
                array(
                    'message'       => 'This form has reached the maximum number of submissions.',
                    'specification' => $formSpecification,
                    'entries'       => $entries,
                )
            );
        }

        $form = $this->getForm(
            'form_specified-form_add',
            array(
                'form'             => $formSpecification,
                'person'           => $person,
                'language'         => $this->getLanguage(),
                'entry'            => $draftVersion,
                'guest_info'       => $guestInfo,
                'ask_student_info' => $formSpecification->getStudentInfo(),
            )
        );

        if (isset($draftVersion)) {
            $form->setAttribute(
                'action',
                $this->url()->fromRoute(
                    'form_view',
                    array(
                        'action' => 'edit',
                        'id'     => $draftVersion->getId(),
                    )
                )
            );
        }

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            $isDraft = $this->getRequest()->getPost()->get('save_as_draft') !== null;

            if ($form->isValid() || $isDraft) {
                $formEntry = new FormEntry($formSpecification, $person);
                if ($person === null) {
                    $formEntry->setGuestInfo(
                        new GuestInfo($this->getEntityManager(), $this->getRequest())
                    );
                }

                $formEntry = $form->hydrateObject($formEntry);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                if ($formSpecification->hasMail() && !$isDraft) {
                    MailHelper::send($formEntry, $formSpecification, $this->getLanguage(), $this->getMailTransport(), $this->url(), $this->getRequest());
                }

                if (!$isDraft) {
                    $this->flashMessenger()->success(
                        'Success',
                        'Your entry has been recorded.'
                    );
                } else {
                    $this->flashMessenger()->success(
                        'Success',
                        'Your entry has been saved.'
                    );
                }

                $this->redirectFormComplete($formSpecification, $group, $progressBarInfo, $isDraft);

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'specification'   => $formSpecification,
                'form'            => $form,
                'entries'         => $entries,
                'group'           => $group,
                'progressBarInfo' => $progressBarInfo,
            )
        );
    }

    public function viewAction()
    {
        $entry = $this->getEntryEntity();
        if ($entry === null) {
            return $this->notFoundAction();
        }

        $entry->getForm()->setEntityManager($this->getEntityManager());

        $now = new DateTime();
        $formClosed = ($now < $entry->getForm()->getStartDate() || $now > $entry->getForm()->getEndDate() || !$entry->getForm()->isActive());

        $group = $this->getGroupEntity($entry->getForm());
        $progressBarInfo = null;

        if ($group !== null) {
            $progressBarInfo = $this->progressBarInfo($group, $entry->getForm());

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                $this->flashMessenger()->warn(
                    'Warning',
                    'Please submit these forms in order.'
                );

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action' => 'index',
                        'id'     => $progressBarInfo['first_uncompleted_id'],
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'formClosed'      => $formClosed,
                'specification'   => $entry->getForm(),
                'group'           => $group,
                'progressBarInfo' => $progressBarInfo,
                'entry'           => $entry,
            )
        );
    }

    public function doodleAction()
    {
        $formSpecification = $this->getFormEntity();
        if ($formSpecification === null) {
            return $this->notFoundAction();
        }

        if ($formSpecification->getType() == 'form') {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action' => 'index',
                    'id'     => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $notValid = false;

        $now = new DateTime();
        if ($now < $formSpecification->getStartDate() || $now > $formSpecification->getEndDate() || !$formSpecification->isActive()) {
            return new ViewModel(
                array(
                    'message'       => 'This form is currently closed.',
                    'specification' => $formSpecification,
                )
            );
        }

        if (!$this->getAuthentication()->isAuthenticated() && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'message'       => 'Please login to view this form.',
                    'specification' => $formSpecification,
                )
            );
        }

        $group = $this->getGroupEntity($formSpecification);
        $progressBarInfo = null;

        if ($group) {
            $progressBarInfo = $this->progressBarInfo($group, $formSpecification);

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                $this->flashMessenger()->warn(
                    'Warning',
                    'Please submit these forms in order.'
                );

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action' => 'index',
                        'id'     => $progressBarInfo['first_uncompleted_id'],
                    )
                );

                return new ViewModel();
            }
        }

        $person = null;
        $formEntry = null;
        $guestInfo = null;
        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndPerson($formSpecification, $person);
        } elseif ($this->isCookieSet()) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($this->getCookie());

            if ($guestInfo) {
                $formEntry = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findOneByFormAndGuestInfo($formSpecification, $guestInfo);
            }
        }

        $form = $this->getForm(
            'form_specified-form_doodle',
            array(
                'form'       => $formSpecification,
                'person'     => $person,
                'language'   => $this->getLanguage(),
                'entry'      => $formEntry,
                'guest_info' => $guestInfo,
            )
        );

        if ($this->getRequest()->isPost() && $formSpecification->canBeSavedBy($person)) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formEntry = new FormEntry($formSpecification, $person);
                if ($person === null) {
                    $formEntry->setGuestInfo(
                        new GuestInfo($this->getEntityManager(), $this->getRequest())
                    );
                }

                $formEntry = $form->hydrateObject($formEntry);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                if ($formSpecification->hasMail()) {
                    MailHelper::send($formEntry, $formSpecification, $this->getLanguage(), $this->getMailTransport(), $this->url(), $this->getRequest());
                }

                $this->flashMessenger()->success(
                    'Success',
                    'Your entry has been recorded.'
                );

                $this->redirectFormComplete($formSpecification, $group, $progressBarInfo);

                return new ViewModel();
            } else {
                $notValid = true;
            }
        }

        return new ViewModel(
            array(
                'specification'   => $formSpecification,
                'form'            => $form,
                'doodleNotValid'  => $notValid,
                'formEntry'       => $formEntry,
                'group'           => $group,
                'progressBarInfo' => $progressBarInfo,
            )
        );
    }

    public function saveDoodleAction()
    {
        $formSpecification = $this->getFormEntity();
        if ($formSpecification === null) {
            return $this->notFoundAction();
        }

        if ($formSpecification->getType() == 'form') {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action' => 'doodle',
                    'id'     => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $now = new DateTime();
        if ($now < $formSpecification->getStartDate() || $now > $formSpecification->getEndDate() || !$formSpecification->isActive()) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        if (!$this->getAuthentication()->isAuthenticated() && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $group = $this->getGroupEntity($formSpecification);

        if ($group !== null) {
            $progressBarInfo = $this->progressBarInfo($group, $formSpecification);

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                return new ViewModel(
                    array(
                        'result' => (object) array('status' => 'error'),
                    )
                );
            }
        }

        $person = null;
        $guestInfo = null;
        $formEntry = null;

        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndPerson($formSpecification, $person);
        } elseif ($this->isCookieSet()) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($this->getCookie());

            if ($guestInfo) {
                $formEntry = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findOneByFormAndGuestInfo($formSpecification, $guestInfo);
            }
        }

        $form = $this->getForm(
            'form_specified-form_doodle',
            array(
                'form'       => $formSpecification,
                'person'     => $person,
                'language'   => $this->getLanguage(),
                'entry'      => $formEntry,
                'guest_info' => $guestInfo,
            )
        );

        if ($this->getRequest()->isPost() && $formSpecification->canBeSavedBy($person)) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($formEntry === null) {
                    $formEntry = new FormEntry($formSpecification, $person);
                    if ($person === null) {
                        $formEntry->setGuestInfo(
                            new GuestInfo($this->getEntityManager(), $this->getRequest())
                        );
                    }
                }

                $formEntry = $form->hydrateObject($formEntry);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                if ($formSpecification->hasMail()) {
                    MailHelper::send($formEntry, $formSpecification, $this->getLanguage(), $this->getMailTransport(), $this->url(), $this->getRequest());
                }

                return new ViewModel(
                    array(
                        'result' => (object) array('status' => 'success'),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'error',
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'error'),
            )
        );
    }

    public function editAction()
    {
        $formEntry = $this->getEntryEntity();
        if ($formEntry === null) {
            return $this->notFoundAction();
        }

        $formEntry->getForm()->setEntityManager($this->getEntityManager());

        $now = new DateTime();
        if ($now < $formEntry->getForm()->getStartDate() || $now > $formEntry->getForm()->getEndDate() || !$formEntry->getForm()->isActive()) {
            return new ViewModel(
                array(
                    'message'       => 'This form is currently closed.',
                    'specification' => $formEntry->getForm(),
                )
            );
        }

        $group = $this->getGroupEntity($formEntry->getForm());
        $progressBarInfo = null;

        if ($group !== null) {
            $progressBarInfo = $this->progressBarInfo($group, $formEntry->getForm());

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                $this->flashMessenger()->warn(
                    'Warning',
                    'Please submit these forms in order.'
                );

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action' => 'index',
                        'id'     => $progressBarInfo['first_uncompleted_id'],
                    )
                );

                return new ViewModel();
            }
        }

        $person = null;
        $guestInfo = null;
        $draftVersion = null;

        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
            $draftVersion = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findDraftVersionByFormAndPerson($formEntry->getForm(), $person);
        } elseif ($this->isCookieSet()) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($this->getCookie());

            if ($guestInfo) {
                $draftVersion = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findDraftVersionByFormAndGuestInfo($formEntry->getForm(), $guestInfo);
            }
        }

        $form = $this->getForm(
            'form_specified-form_edit',
            array(
                'form'       => $formEntry->getForm(),
                'person'     => $person,
                'language'   => $this->getLanguage(),
                'entry'      => $formEntry,
                'guest_info' => $guestInfo,
                'is_draft'   => isset($draftVersion) && $draftVersion != $formEntry,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            $isDraft = $this->getRequest()->getPost()->get('save_as_draft') !== null;

            if ($form->isValid() || $isDraft) {
                $formEntry = $form->hydrateObject($formEntry);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                if ($formEntry->getForm()->hasMail() && !$isDraft) {
                    MailHelper::send($formEntry, $formEntry->getForm(), $this->getLanguage(), $this->getMailTransport(), $this->url(), $this->getRequest());
                }

                if (!$isDraft) {
                    $this->flashMessenger()->success(
                        'Success',
                        'Your entry has been updated.'
                    );
                } else {
                    $this->flashMessenger()->success(
                        'Success',
                        'Your entry has been saved.'
                    );
                }

                $this->redirectFormComplete($formEntry->getForm(), $group, $progressBarInfo, $isDraft);

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'specification'   => $formEntry->getForm(),
                'form'            => $form,
                'group'           => $group,
                'progressBarInfo' => $progressBarInfo,
            )
        );
    }

    public function loginAction()
    {
        $form = $this->getFormEntity();
        if ($form === null) {
            return $this->notFoundAction();
        }

        if ($this->getParam('key') === null) {
            return $this->notFoundAction();
        }

        $guestInfo = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\GuestInfo')
            ->findOneByFormAndSessionId($form, $this->getParam('key'));

        if ($guestInfo !== null) {
            $guestInfo->renew($this->getRequest());
        } else {
            return $this->notFoundAction();
        }

        $this->redirect()->toRoute(
            'form_view',
            array(
                'action' => 'index',
                'id'     => $form->getId(),
            )
        );

        return new ViewModel();
    }

    public function downloadFileAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('form.file_upload_path') . '/' . $this->getParam('id');

        $fieldEntry = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findOneByValue($this->getParam('id'));

        if ($fieldEntry === null || !$this->getAuthentication()->isAuthenticated()) {
            return $this->notFoundAction();
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $fieldEntry->getReadableValue() . '"',
                'Content-Type'        => mime_content_type($filePath),
                'Content-Length'      => filesize($filePath),
            )
        );
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath, 'r');
        $data = fread($handle, filesize($filePath));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    /**
     * @return Form|null
     */
    private function getFormEntity()
    {
        $form = $this->getEntityById('FormBundle\Entity\Node\Form');

        if (!($form instanceof Form)) {
            return;
        }

        $form->setEntityManager($this->getEntityManager());

        return $form;
    }

    /**
     * @return FormEntry|null
     */
    private function getEntryEntity()
    {
        $entry = $this->getEntityById('FormBundle\Entity\Node\Entry');

        if (!($entry instanceof FormEntry) || (!$entry->getForm()->isEditableByUser() && !$entry->isDraft() && $this->getParam('action') != 'view')) {
            return;
        }

        $now = new DateTime();
        if ($now < $entry->getForm()->getStartDate() || $now > $entry->getForm()->getEndDate() || !$entry->getForm()->isActive()) {
            return;
        }

        $person = null;
        $guestInfo = null;

        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
        }

        if ($this->isCookieSet() && $person === null) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($this->getCookie());
        }

        if ($person !== null && $entry->getCreationPerson() != $person) {
            return;
        } elseif ($guestInfo !== null && $entry->getGuestInfo() !== $guestInfo) {
            return;
        } elseif ($guestInfo === null && $person === null) {
            return;
        }

        return $entry;
    }

    /**
     * @param  Form $form
     * @return Group|null
     */
    private function getGroupEntity(Form $form)
    {
        $mapping = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
            ->findOneByForm($form);

        if ($mapping !== null) {
            return $mapping->getGroup();
        }
    }

    /**
     * @param  Group $group
     * @param  Form  $form
     * @return array
     */
    private function progressBarInfo(Group $group, Form $form)
    {
        $data = array(
            'uncompleted_before_current' => 0,
            'first_uncompleted_id'       => 0,
            'completed_before_current'   => 0,
            'previous_form'              => 0,
            'current_form'               => $group->getFormNumber($form),
            'current_completed'          => false,
            'current_draft'              => false,
            'next_form'                  => 0,
            'completed_after_current'    => 0,
            'total_forms'                => count($group->getForms()),
        );

        if ($this->getAuthentication()->isAuthenticated()) {
            foreach ($group->getForms() as $groupForm) {
                $formEntry = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findAllByFormAndPerson($groupForm->getForm(), $this->getAuthentication()->getPersonObject());

                $draftVersion = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findDraftVersionByFormAndPerson($groupForm->getForm(), $this->getAuthentication()->getPersonObject());

                if ($data['current_form'] == $group->getFormNumber($groupForm->getForm())) {
                    $data['current_completed'] = count($formEntry) > 0 && $draftVersion === null;
                    $data['current_draft'] = $draftVersion !== null;
                } elseif ($data['current_form'] > $group->getFormNumber($groupForm->getForm())) {
                    $data['previous_form'] = $groupForm->getForm()->getId();
                    if (count($formEntry) > 0 && $draftVersion === null) {
                        $data['completed_before_current']++;
                    } else {
                        $data['uncompleted_before_current']++;
                        if ($data['first_uncompleted_id'] == 0) {
                            $data['first_uncompleted_id'] = $groupForm->getForm()->getId();
                        }
                    }
                } else {
                    if (count($formEntry) > 0 && $draftVersion === null) {
                        $data['completed_after_current']++;
                    }
                    if ($data['next_form'] == 0) {
                        $data['next_form'] = $groupForm->getForm()->getId();
                    }
                }
            }
        } else {
            $guestInfo = null;
            if ($this->isCookieSet()) {
                $guestInfo = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\GuestInfo')
                    ->findOneBySessionId($this->getCookie());

                $guestInfo->renew($this->getRequest());
            }

            foreach ($group->getForms() as $groupForm) {
                $formEntry = array();
                if ($guestInfo !== null) {
                    $formEntry = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Node\Entry')
                        ->findAllByFormAndGuestInfo($groupForm->getForm(), $guestInfo);

                    $draftVersion = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Node\Entry')
                        ->findDraftVersionByFormAndGuestInfo($groupForm->getForm(), $guestInfo);
                }

                if ($data['current_form'] == $group->getFormNumber($groupForm->getForm())) {
                    $data['current_completed'] = count($formEntry) > 0 && !isset($draftVersion);
                    $data['current_draft'] = isset($draftVersion);
                } elseif ($data['current_form'] > $group->getFormNumber($groupForm->getForm())) {
                    $data['previous_form'] = $groupForm->getForm()->getId();

                    if (count($formEntry) > 0 && !isset($draftVersion)) {
                        $data['completed_before_current']++;
                    } else {
                        $data['uncompleted_before_current']++;
                        if ($data['first_uncompleted_id'] == 0) {
                            $data['first_uncompleted_id'] = $groupForm->getForm()->getId();
                        }
                    }
                } else {
                    if (count($formEntry) > 0 && !isset($draftVersion)) {
                        $data['completed_after_current']++;
                    }
                    if ($data['next_form'] == 0) {
                        $data['next_form'] = $groupForm->getForm()->getId();
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param  Form       $formSpecification
     * @param  Group|null $group
     * @param  array|null $progressBarInfo
     * @param  boolean    $draft
     * @return null
     */
    private function redirectFormComplete(Form $formSpecification, Group $group = null, $progressBarInfo = null, $draft = false)
    {
        if ($group && !$draft && isset($progressBarInfo['next_form'])) {
            if ($progressBarInfo['next_form'] == 0) {
                $this->redirect()->toRoute(
                    'form_group',
                    array(
                        'action' => 'view',
                        'id'     => $group->getId(),
                    )
                );
            } else {
                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action' => 'index',
                        'id'     => $progressBarInfo['next_form'],
                    )
                );
            }
        } else {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action' => 'index',
                    'id'     => $formSpecification->getId(),
                )
            );
        }
    }

    /**
     * @return boolean
     */
    private function isCookieSet()
    {
        $cookie = $this->getRequest()->getCookie();

        return $cookie !== false && $cookie->offsetExists(GuestInfo::$cookieNamespace);
    }

    /**
     * @return string
     */
    private function getCookie()
    {
        $cookie = $this->getRequest()->getCookie();

        return $cookie !== false && $cookie->offsetExists(GuestInfo::$cookieNamespace);
    }
}
