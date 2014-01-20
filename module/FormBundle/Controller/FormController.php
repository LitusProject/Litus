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
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Group,
    FormBundle\Entity\Node\GuestInfo,
    FormBundle\Entity\Node\Entry as FormEntry,
    FormBundle\Entity\Entry as FieldEntry,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Form\SpecifiedForm\Add as AddForm,
    FormBundle\Form\SpecifiedForm\Doodle as DoodleForm,
    FormBundle\Form\SpecifiedForm\Edit as EditForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Http\Headers,
    Zend\Mail\Message,
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

            $entries = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findAllByFormAndGuestInfo($formSpecification, $guestInfo);
            $draftVersion = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findDraftVersionByFormAndGuestInfo($formSpecification, $guestInfo);
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
            }
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

                if ($person === null && $guestInfo == null) {
                    $guestInfo = new GuestInfo(
                        $this->getEntityManager(),
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['email']
                    );
                    $this->getEntityManager()->persist($guestInfo);
                }

                $formEntry = new FormEntry($person, $guestInfo, $formSpecification, isset($formData['save_as_draft']));

                $this->getEntityManager()->persist($formEntry);

                foreach ($formSpecification->getFields() as $field) {
                    $value = $formData['field-' . $field->getId()];

                    if ($field instanceof FileField) {
                        $value = '';
                        $filePath = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('form.file_upload_path');

                        $upload = new FileUpload();
                        $upload->setValidators($form->getInputFilter()->get('field-' . $field->getId())->getValidatorChain()->getValidators());
                        if ($upload->isValid('field-' . $field->getId())) {
                            $fileName = '';
                            do{
                                $fileName = sha1(uniqid());
                            } while (file_exists($filePath . '/' . $fileName));

                            $upload->addFilter('Rename', $filePath . '/' . $fileName, 'field-' . $field->getId());
                            $upload->receive('field-' . $field->getId());

                            $value = $fileName;
                        }
                        $errors = $upload->getMessages();

                        if (!$field->isRequired() && isset($errors['fileUploadErrorNoFile']))
                            unset($errors['fileUploadErrorNoFile']);

                        if (sizeof($errors) > 0) {
                            $form->setMessages(array('field-' . $field->getId() => $errors));

                            return new ViewModel(
                                array(
                                    'specification' => $formSpecification,
                                    'form'          => $form,
                                    'entries'       => $entries,
                                )
                            );
                        }
                    }

                    $fieldEntry = new FieldEntry($formEntry, $field, $value);

                    $formEntry->addFieldEntry($fieldEntry);

                    $this->getEntityManager()->persist($fieldEntry);
                }

                $this->getEntityManager()->flush();

                if (!isset($formData['save_as_draft'])) {
                    if ($formSpecification->hasMail()) {
                        $mailAddress = $formSpecification->getMail()->getFrom();

                        $mail = new Message();
                        $mail->setBody($formSpecification->getCompletedMailBody($formEntry, $this->getLanguage()))
                            ->setFrom($mailAddress)
                            ->setSubject($formSpecification->getMail()->getSubject())
                            ->addTo($formEntry->getPersonInfo()->getEmail(), $formEntry->getPersonInfo()->getFullName());

                        if ($formSpecification->getMail()->getBcc())
                            $mail->addBcc($mailAddress);

                        if ('development' != getenv('APPLICATION_ENV'))
                            $this->getMailTransport()->send($mail);
                    }

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

                if ($group && !isset($formData['save_as_draft'])) {
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

        $formEntries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($formSpecification);

        $occupiedSlots = array();
        foreach($formEntries as $formEntry) {
            if ($formEntry->getCreationPerson() == $person)
                continue;

            foreach($formEntry->getFieldEntries() as $fieldEntry) {
                $occupiedSlots[$fieldEntry->getField()->getId()] = $formEntry->getPersonInfo()->getFullName();
            }
        }

        if (null !== $person) {
            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndPerson($formSpecification, $person);
        } elseif(isset($_COOKIE['LITUS_form'])) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($_COOKIE['LITUS_form']);

            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndGuestInfo($formSpecification, $guestInfo);
        }

        $form = new DoodleForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $person, $formEntry, $occupiedSlots);
        if (isset($guestInfo))
            $form->populateFromGuestInfo($guestInfo);

        if ($this->getRequest()->isPost() && $formSpecification->canBeSavedBy($person)) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($person === null && $guestInfo == null) {
                    $guestInfo = new GuestInfo(
                        $this->getEntityManager(),
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['email']
                    );
                    $this->getEntityManager()->persist($guestInfo);
                }

                if (null === $formEntry) {
                    $formEntry = new FormEntry($person, $guestInfo, $formSpecification);
                    $this->getEntityManager()->persist($formEntry);
                } else {
                    foreach($formEntry->getFieldEntries() as $fieldEntry) {
                        $this->getEntityManager()->remove($fieldEntry);
                    }
                    $this->getEntityManager()->flush();
                }

                foreach ($formSpecification->getFields() as $field) {
                    if (isset($formData['field-' . $field->getId()]) && $formData['field-' . $field->getId()]) {
                        $fieldEntry = new FieldEntry($formEntry, $field, '1');
                        $formEntry->addFieldEntry($fieldEntry);
                        $this->getEntityManager()->persist($fieldEntry);

                        if (!$formSpecification->isMultiple())
                            break;
                    }
                }

                $this->getEntityManager()->flush();

                if ($formSpecification->hasMail()) {
                    $mailAddress = $formSpecification->getMail()->getFrom();

                    $mail = new Message();
                    $mail->setBody($formSpecification->getCompletedMailBody($formEntry, $this->getLanguage()))
                        ->setFrom($mailAddress)
                        ->setSubject($formSpecification->getMail()->getSubject())
                        ->addTo($formEntry->getPersonInfo()->getEmail(), $formEntry->getPersonInfo()->getFullName());

                    if ($formSpecification->getMail()->getBcc())
                        $mail->addBcc($mailAddress);

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);
                }

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'Your entry has been recorded.'
                    )
                );

                if ($group) {
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

                return new ViewModel();
            } else {
                $notValid = true;
            }
        }

        return new ViewModel(
            array(
                'specification'   => $formSpecification,
                'form'            => $form,
                'occupiedSlots'   => $occupiedSlots,
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

        $occupiedSlots = array();
        foreach($formEntries as $formEntry) {
            if ($formEntry->getCreationPerson() == $person)
                continue;

            foreach($formEntry->getFieldEntries() as $fieldEntry) {
                $occupiedSlots[$fieldEntry->getField()->getId()] = $formEntry->getPersonInfo()->getFullName();
            }
        }

        if (null !== $person) {
            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndPerson($formSpecification, $person);
        } elseif(isset($_COOKIE['LITUS_form'])) {
            $guestInfo = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\GuestInfo')
                ->findOneBySessionId($_COOKIE['LITUS_form']);

            $formEntry = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findOneByFormAndGuestInfo($formSpecification, $guestInfo);
        }

        $form = new DoodleForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $person, $formEntry, $occupiedSlots);
        if (isset($guestInfo))
            $form->populateFromGuestInfo($guestInfo);

        if ($this->getRequest()->isPost() && $formSpecification->canBeSavedBy($person)) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($person === null && $guestInfo == null) {
                    $guestInfo = new GuestInfo(
                        $this->getEntityManager(),
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['email']
                    );
                    $this->getEntityManager()->persist($guestInfo);
                }

                if (null === $formEntry) {
                    $formEntry = new FormEntry($person, $guestInfo, $formSpecification);
                    $this->getEntityManager()->persist($formEntry);
                } else {
                    foreach($formEntry->getFieldEntries() as $fieldEntry) {
                        $this->getEntityManager()->remove($fieldEntry);
                    }
                    $this->getEntityManager()->flush();
                }

                foreach ($formSpecification->getFields() as $field) {
                    if (isset($formData['field-' . $field->getId()]) && $formData['field-' . $field->getId()]) {
                        $fieldEntry = new FieldEntry($formEntry, $field, '1');
                        $formEntry->addFieldEntry($fieldEntry);
                        $this->getEntityManager()->persist($fieldEntry);

                        if (!$formSpecification->isMultiple())
                            break;
                    }
                }

                $this->getEntityManager()->flush();

                if ($formSpecification->hasMail()) {
                    $mailAddress = $formSpecification->getMail()->getFrom();

                    $mail = new Message();
                    $mail->setBody($formSpecification->getCompletedMailBody($formEntry, $this->getLanguage()))
                        ->setFrom($mailAddress)
                        ->setSubject($formSpecification->getMail()->getSubject())
                        ->addTo($formEntry->getPersonInfo()->getEmail(), $formEntry->getPersonInfo()->getFullName());

                    if ($formSpecification->getMail()->getBcc())
                        $mail->addBcc($mailAddress);

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);
                }

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

            $draftVersion = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findDraftVersionByFormAndGuestInfo($entry->getForm(), $guestInfo);
        }

        $form = new EditForm($this->getEntityManager(), $this->getLanguage(), $entry->getForm(), $entry, $person);
        $form->hasDraft(null !== $draftVersion && $draftVersion != $entry);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid() || isset($formData['save_as_draft'])) {
                if ($entry->isGuestEntry()) {
                    $entry->getGuestInfo()
                        ->setFirstName($formData['first_name'])
                        ->setLastName($formData['last_name'])
                        ->setEmail($formData['email']);
                }

                $formData = $form->getFormData($formData);

                $entry->setDraft(isset($formData['save_as_draft']));

                foreach ($entry->getForm()->getFields() as $field) {
                    $value = $formData['field-' . $field->getId()];

                    $fieldEntry = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Entry')
                        ->findOneByFormEntryAndField($entry, $field);

                    if ($field instanceof FileField) {
                        $filePath = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('form.file_upload_path');

                        $upload = new FileUpload();
                        $upload->setValidators($form->getInputFilter()->get('field-' . $field->getId())->getValidatorChain()->getValidators());
                        if ($upload->isValid('field-' . $field->getId())) {
                            if ($fieldEntry->getValue() == '') {
                                $fileName = '';
                                do{
                                    $fileName = sha1(uniqid());
                                } while (file_exists($filePath . '/' . $fileName));
                            } else {
                                $fileName = $fieldEntry->getValue();
                            }

                            if (file_exists($filePath . '/' . $fileName))
                                unlink($filePath . '/' . $fileName);
                            $upload->addFilter('Rename', $filePath . '/' . $fileName, 'field-' . $field->getId());
                            $upload->receive('field-' . $field->getId());

                            $value = $fileName;
                        }

                        $errors = $upload->getMessages();

                        if (isset($errors['fileUploadErrorNoFile']))
                            unset($errors['fileUploadErrorNoFile']);

                        if (sizeof($errors) > 0) {
                            $form->setMessages(array('field-' . $field->getId() => $errors));

                            return new ViewModel(
                                array(
                                    'specification' => $entry->getForm(),
                                    'form'          => $form,
                                )
                            );
                        } elseif ($value == '') {
                            $value = $fieldEntry->getValue();
                        }
                    }

                    if ($fieldEntry) {
                        $fieldEntry->setValue($value);
                    } else {
                        $fieldEntry = new FieldEntry($entry, $field, $value);
                        $entry->addFieldEntry($fieldEntry);
                        $this->getEntityManager()->persist($fieldEntry);
                    }
                }

                $this->getEntityManager()->flush();

                if (!isset($formData['save_as_draft'])) {
                    if ($entry->getForm()->hasMail()) {
                        $mailAddress = $entry->getForm()->getMail()->getFrom();

                        $mail = new Message();
                        $mail->setBody($entry->getForm()->getCompletedMailBody($entry, $this->getLanguage()))
                            ->setFrom($mailAddress)
                            ->setSubject($entry->getForm()->getMail()->getSubject())
                            ->addTo($entry->getPersonInfo()->getEmail(), $entry->getPersonInfo()->getFullName());

                        if ($entry->getForm()->getMail()->getBcc())
                            $mail->addBcc($mailAddress);

                        if ('development' != getenv('APPLICATION_ENV'))
                            $this->getMailTransport()->send($mail);
                    }

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

                if ($group && !isset($formData['save_as_draft'])) {
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
                            'id'       => $entry->getForm()->getId(),
                        )
                    );
                }

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
        if(isset($_COOKIE['LITUS_form'])) {
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
}
