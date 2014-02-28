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

namespace FormBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    FormBundle\Component\Form\Form as FormHelper,
    FormBundle\Component\Form\Doodle as DoodleHelper,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Group,
    FormBundle\Form\SpecifiedForm\Add as AddForm,
    FormBundle\Form\SpecifiedForm\Doodle as DoodleForm,
    FormBundle\Form\SpecifiedForm\Edit as EditForm,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * FormController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FormController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        if ($formSpecification->getType() == 'doodle') {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action'   => 'doodle',
                    'id'       => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $entries = null;

        $now = new DateTime();
        if ($now < $formSpecification->getStartDate() || $now > $formSpecification->getEndDate() || !$formSpecification->isActive()) {
            return new ViewModel(
                array(
                    'message'       => 'This form is currently closed.',
                    'specification' => $formSpecification,
                )
            );
        }

        $person = $this->getAuthentication()->getPersonObject();
        $guestInfo = null;

        if (null !== $person) {
            $entries = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findAllByFormAndPerson($formSpecification, $person);
            $draftVersion = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findDraftVersionByFormAndPerson($formSpecification, $person);
        } elseif(isset($_COOKIE['LITUS_form'])) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($_COOKIE['LITUS_form']);

            if ($guestInfo) {
                $entries = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findAllByFormAndGuestInfo($formSpecification, $guestInfo);
                $draftVersion = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findDraftVersionByFormAndGuestInfo($formSpecification, $guestInfo);
            }
        }

        $group = $this->_getGroup($formSpecification);
        $progressBarInfo = null;

        if ($group) {
            $progressBarInfo = $this->_progressBarInfo($group, $formSpecification);

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::WARNING,
                        'Warning',
                        'Please submit these forms in order.'
                    )
                );

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action'   => 'view',
                        'id'       => $progressBarInfo['first_uncompleted_id'],
                    )
                );

                return new ViewModel();
            }
        }

        if (null === $person && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'message'       => 'Please login to view this form.',
                    'specification' => $formSpecification,
                )
            );
        } elseif (!$formSpecification->isMultiple() && count($entries) > 0 && null === $draftVersion) {
            return new ViewModel(
                array(
                    'message'       => 'You can\'t fill this form more than once.',
                    'specification' => $formSpecification,
                    'entries'       => $entries,
                    'group'           => $group,
                    'progressBarInfo' => $progressBarInfo,
                )
            );
        }

        $entriesCount = count($this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($formSpecification));

        if ($formSpecification->getMax() != 0 && $entriesCount >= $formSpecification->getMax()) {
            return new ViewModel(
                array(
                    'message'       => 'This form has reached the maximum number of submissions.',
                    'specification' => $formSpecification,
                    'entries'       => $entries,
                )
            );
        }

        $form = new AddForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $person);
        if (isset($draftVersion)) {
            $form->populateFromEntry($draftVersion);
            $form->setAttribute(
                'action',
                $this->url()->fromRoute(
                    'form_view',
                    array(
                        'action' => 'edit',
                        'id' => $draftVersion->getId(),
                    )
                )
            );
        }

        if (isset($guestInfo))
            $form->populateFromGuestInfo($guestInfo);

        if ($this->getRequest()->isPost()) {
            $formData = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($formData);

            if ($form->isValid() || isset($formData['save_as_draft'])) {
                $formData = $form->getFormData($formData);

                $result = FormHelper::save(null, $person, $guestInfo, $formSpecification, $formData, $this->getLanguage(), $form, $this->getEntityManager(), $this->getMailTransport());

                if (!$result) {
                    return new ViewModel(
                        array(
                            'specification' => $formSpecification,
                            'form'          => $form,
                            'entries'       => $entries,
                        )
                    );
                }

                if (!isset($formData['save_as_draft'])) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'Your entry has been recorded.'
                        )
                    );
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'Your entry has been saved.'
                        )
                    );
                }

                $this->_redirectFormComplete($group, $progressBarInfo, $formSpecification, isset($formData['save_as_draft']));

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

    public function doodleAction()
    {
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        if ($formSpecification->getType() == 'form') {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action'   => 'view',
                    'id'       => $formSpecification->getId(),
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

        $person = $this->getAuthentication()->getPersonObject();
        $guestInfo = null;

        if ($person === null && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'message'       => 'Please login to view this form.',
                    'specification' => $formSpecification,
                )
            );
        }

        $group = $this->_getGroup($formSpecification);
        $progressBarInfo = null;

        if ($group) {
            $progressBarInfo = $this->_progressBarInfo($group, $formSpecification);

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::WARNING,
                        'Warning',
                        'Please submit these forms in order.'
                    )
                );

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action'   => 'view',
                        'id'       => $progressBarInfo['first_uncompleted_id'],
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
        }

        $formEntry = null;
        if (null !== $person) {
            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndPerson($formSpecification, $person);
        } elseif(isset($_COOKIE['LITUS_form'])) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($_COOKIE['LITUS_form']);

            if ($guestInfo) {
                $formEntry = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findOneByFormAndGuestInfo($formSpecification, $guestInfo);
            }
        }

        $form = new DoodleForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $person, $formEntry);
        if (isset($guestInfo))
            $form->populateFromGuestInfo($guestInfo);

        if ($this->getRequest()->isPost() && $formSpecification->canBeSavedBy($person)) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);
                DoodleHelper::save($formEntry, $person, $guestInfo, $formSpecification, $formData, $this->getLanguage(), $this->getEntityManager(), $this->getMailTransport());

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'Your entry has been recorded.'
                    )
                );

                $this->_redirectFormComplete($group, $progressBarInfo, $formSpecification);

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
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        if ($formSpecification->getType() == 'form') {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action'   => 'doodle',
                    'id'       => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $notValid = false;

        $now = new DateTime();
        if ($now < $formSpecification->getStartDate() || $now > $formSpecification->getEndDate() || !$formSpecification->isActive()) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $person = $this->getAuthentication()->getPersonObject();
        $guestInfo = null;

        if ($person === null && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $group = $this->_getGroup($formSpecification);
        $progressBarInfo = null;

        if ($group) {
            $progressBarInfo = $this->_progressBarInfo($group, $formSpecification);

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                return new ViewModel(
                    array(
                        'result' => (object) array('status' => 'error'),
                    )
                );
            }
        }

        $formEntries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($formSpecification);

        $formEntry = null;
        if (null !== $person) {
            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndPerson($formSpecification, $person);
        } elseif(isset($_COOKIE['LITUS_form'])) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($_COOKIE['LITUS_form']);

            if ($guestInfo) {
                $formEntry = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findOneByFormAndGuestInfo($formSpecification, $guestInfo);
            }
        }

        $form = new DoodleForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $person, $formEntry);
        if (isset($guestInfo))
            $form->populateFromGuestInfo($guestInfo);

        if ($this->getRequest()->isPost() && $formSpecification->canBeSavedBy($person)) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);
                DoodleHelper::save($formEntry, $person, $guestInfo, $formSpecification, $formData, $this->getLanguage(), $this->getEntityManager(), $this->getMailTransport());

                return new ViewModel(
                    array(
                        'result' => (object) array('status' => 'success'),
                    )
                );

                return new ViewModel();
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

                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'error',
                            'errors' => $formErrors,
                        )
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
        if (!($entry = $this->_getEntry()))
            return new ViewModel();

        $entry->getForm()->setEntityManager($this->getEntityManager());

        $now = new DateTime();
        if ($now < $entry->getForm()->getStartDate() || $now > $entry->getForm()->getEndDate() || !$entry->getForm()->isActive()) {
            return new ViewModel(
                array(
                    'message'       => 'This form is currently closed.',
                    'specification' => $entry->getForm(),
                )
            );
        }

        $group = $this->_getGroup($entry->getForm());
        $progressBarInfo = null;

        if ($group) {
            $progressBarInfo = $this->_progressBarInfo($group, $entry->getForm());

            if ($progressBarInfo['uncompleted_before_current'] > 0) {
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::WARNING,
                        'Warning',
                        'Please submit these forms in order.'
                    )
                );

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action'   => 'view',
                        'id'       => $progressBarInfo['first_uncompleted_id'],
                    )
                );

                return new ViewModel();
            }
        }

        $person = $this->getAuthentication()->getPersonObject();
        $guestInfo = null;

        if (null !== $person) {
            $draftVersion = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findDraftVersionByFormAndPerson($entry->getForm(), $person);
        } elseif(isset($_COOKIE['LITUS_form'])) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($_COOKIE['LITUS_form']);

            if ($guestInfo) {
                $draftVersion = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findDraftVersionByFormAndGuestInfo($entry->getForm(), $guestInfo);
            }
        }

        $form = new EditForm($this->getEntityManager(), $this->getLanguage(), $entry->getForm(), $entry, $person);
        $form->hasDraft(null !== $draftVersion && $draftVersion != $entry);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid() || isset($formData['save_as_draft'])) {
                $formData = $form->getFormData($formData);

                $result = FormHelper::save($entry, $person, $guestInfo, $entry->getForm(), $formData, $this->getLanguage(), $form, $this->getEntityManager(), $this->getMailTransport());

                if (!$result) {
                    return new ViewModel(
                        array(
                            'specification' => $formSpecification,
                            'form'          => $form,
                        )
                    );
                }

                if (!isset($formData['save_as_draft'])) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'Your entry has been updated.'
                        )
                    );
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'Your entry has been saved.'
                        )
                    );
                }

                $this->_redirectFormComplete($group, $progressBarInfo, $entry->getForm(), isset($formData['save_as_draft']));

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'specification'   => $entry->getForm(),
                'form'            => $form,
                'group'           => $group,
                'progressBarInfo' => $progressBarInfo,
            )
        );
    }

    public function downloadFileAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('form.file_upload_path') . '/' . $this->getParam('id');

        $fieldEntry = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findOneByValue($this->getParam('id'));

        if (null === $fieldEntry || $fieldEntry->getFormEntry()->getCreationPerson() != $this->getAuthentication()->getPersonObject()) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $this->getParam('id') . '"',
            'Content-Type' => mime_content_type($filePath),
            'Content-Length' => filesize($filePath),
        ));
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

    private function _getForm()
    {
        if (null === $this->getParam('id')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findOneById($this->getParam('id'));

        if (null === $form) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form->setEntityManager($this->getEntityManager());

        return $form;
    }

    private function _getEntry()
    {
        if (null === $this->getParam('id')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $entry = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findOneById($this->getParam('id'));

        if (null === $entry || !$entry->getForm()->isEditableByUser()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $now = new DateTime();
        if ($now < $entry->getForm()->getStartDate() || $now > $entry->getForm()->getEndDate() || !$entry->getForm()->isActive()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $person = $this->getAuthentication()->getPersonObject();
        $guestInfo = null;
        if(isset($_COOKIE['LITUS_form']) && null === $person) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($_COOKIE['LITUS_form']);
        }

        if ($person !== null && $entry->getCreationPerson() != $person) {
            $this->getResponse()->setStatusCode(404);
            return;
        } elseif ($guestInfo !== null && $entry->getGuestInfo() !== $guestInfo) {
            $this->getResponse()->setStatusCode(404);
            return;
        } elseif ($guestInfo === null && $person === null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return $entry;
    }

    private function _getGroup(Form $form)
    {
        $mapping = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($form);

        if (null !== $mapping) {
            return $mapping->getGroup();
        }
    }

    private function _progressBarInfo(Group $group, Form $form)
    {
        $data = array(
            'uncompleted_before_current' => 0,
            'first_uncompleted_id' => 0,
            'completed_before_current' => 0,
            'previous_form' => 0,
            'current_form' => $group->getFormNumber($form),
            'current_completed' => false,
            'current_draft' => false,
            'next_form' => 0,
            'completed_after_current' => 0,
            'total_forms' => sizeof($group->getForms()),
        );

        if ($this->getAuthentication()->isAuthenticated()) {
            foreach($group->getForms() as $groupForm) {
                $formEntry = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findAllByFormAndPerson($groupForm->getForm(), $this->getAuthentication()->getPersonObject());

                $draftVersion = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findDraftVersionByFormAndPerson($groupForm->getForm(), $this->getAuthentication()->getPersonObject());

                if ($data['current_form'] == $group->getFormNumber($groupForm->getForm())) {
                    $data['current_completed'] = (sizeof($formEntry) > 0) && $draftVersion === null;
                    $data['current_draft'] = $draftVersion !== null;
                } elseif ($data['current_form'] > $group->getFormNumber($groupForm->getForm())) {
                    $data['previous_form'] = $groupForm->getForm()->getId();
                    if (sizeof($formEntry) > 0 && null === $draftVersion) {
                        $data['completed_before_current']++;
                    } else {
                        $data['uncompleted_before_current']++;
                        if ($data['first_uncompleted_id'] == 0)
                            $data['first_uncompleted_id'] = $groupForm->getForm()->getId();
                    }
                } else {
                    if (sizeof($formEntry) > 0 && null === $draftVersion)
                        $data['completed_after_current']++;
                    if ($data['next_form'] == 0)
                        $data['next_form'] = $groupForm->getForm()->getId();
                }
            }
        } else {
            $guestInfo = null;
            if(isset($_COOKIE['LITUS_form'])) {
                $guestInfo = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\GuestInfo')
                    ->findOneBySessionId($_COOKIE['LITUS_form']);

                $guestInfo->renew();
            }

            foreach($group->getForms() as $groupForm) {
                $formEntry = array();
                if (null !== $guestInfo) {
                    $formEntry = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Node\Entry')
                        ->findAllByFormAndGuestInfo($groupForm->getForm(), $guestInfo);

                    $draftVersion = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Node\Entry')
                        ->findDraftVersionByFormAndGuestInfo($groupForm->getForm(), $guestInfo);
                }

                if ($data['current_form'] == $group->getFormNumber($groupForm->getForm())) {
                    $data['current_completed'] = (sizeof($formEntry) > 0) && !isset($draftVersion);
                    $data['current_draft'] = isset($draftVersion);
                } elseif ($data['current_form'] > $group->getFormNumber($groupForm->getForm())) {
                    $data['previous_form'] = $groupForm->getForm()->getId();

                    if (sizeof($formEntry) > 0 && !isset($draftVersion)) {
                        $data['completed_before_current']++;
                    } else {
                        $data['uncompleted_before_current']++;
                        if ($data['first_uncompleted_id'] == 0)
                            $data['first_uncompleted_id'] = $groupForm->getForm()->getId();
                    }
                } else {
                    if (sizeof($formEntry) > 0 && !isset($draftVersion))
                        $data['completed_after_current']++;
                    if ($data['next_form'] == 0)
                        $data['next_form'] = $groupForm->getForm()->getId();
                }
            }
        }

        return $data;
    }

    private function _redirectFormComplete(Group $group = null, $progressBarInfo, Form $formSpecification, $draft = false)
    {
        if ($group && !$draft) {
            if ($progressBarInfo['next_form'] == 0) {
                $this->redirect()->toRoute(
                    'form_group',
                    array(
                        'action'   => 'view',
                        'id'       => $group->getId(),
                    )
                );
            } else {
                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action'   => 'view',
                        'id'       => $progressBarInfo['next_form'],
                    )
                );
            }
        } else {
            $this->redirect()->toRoute(
                'form_view',
                array(
                    'action'   => 'view',
                    'id'       => $formSpecification->getId(),
                )
            );
        }
    }
}
