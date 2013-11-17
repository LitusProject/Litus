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

namespace FormBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    FormBundle\Entity\Node\Form,
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

        if (null !== $person) {
            $entries = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Entry')
                ->findAllByFormAndPerson($formSpecification, $person);
        }

        $group = $this->_getGroup($formSpecification);

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

        if ($person === null && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'message'       => 'Please login to view this form.',
                    'specification' => $formSpecification,
                )
            );
        } elseif (null !== $person) {
            if (!$formSpecification->isMultiple() && count($entries) > 0) {
                return new ViewModel(
                    array(
                        'message'       => 'You can\'t fill this form more than once.',
                        'specification' => $formSpecification,
                        'entries'       => $entries,
                    )
                );
            }
        }

        $form = new AddForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $person);

        if ($this->getRequest()->isPost()) {
            $formData = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $guestInfo = null;

                if ($person === null) {
                    $guestInfo = new GuestInfo(
                        $formData['first_name'],
                        $formData['last_name'],
                        $formData['email']
                    );
                    $this->getEntityManager()->persist($guestInfo);
                }

                $formEntry = new FormEntry($person, $guestInfo, $formSpecification);

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

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action'   => 'view',
                        'id'       => $formSpecification->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'specification' => $formSpecification,
                'form'          => $form,
                'entries'       => $entries,
                'group'         => $group,
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

        $group = $this->_getGroup($formSpecification);

        $person = $this->getAuthentication()->getPersonObject();

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

        $formEntry = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findOneByFormAndPerson($formSpecification, $person);

        $form = new DoodleForm($this->getEntityManager(), $this->getLanguage(), $formSpecification, $person, $formEntry, $occupiedSlots);

        if ($this->getRequest()->isPost() && $formSpecification->canBeSavedBy($person)) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $guestInfo = null;
                // Create non-member entry
                if ($person === null) {
                    $guestInfo = new GuestInfo(
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

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action'   => 'doodle',
                        'id'       => $formSpecification->getId(),
                    )
                );

                return new ViewModel();
            } else {
                print_r($form->getMessages());
                $notValid = true;
            }
        }

        return new ViewModel(
            array(
                'specification'  => $formSpecification,
                'form'           => $form,
                'occupiedSlots'  => $occupiedSlots,
                'doodleNotValid' => $notValid,
                'formEntry'      => $formEntry,
                'group'          => $group,
            )
        );
    }

    public function editAction()
    {
        if (!($entry = $this->_getEntry()))
            return new ViewModel();

        $now = new DateTime();
        if ($now < $entry->getForm()->getStartDate() || $now > $entry->getForm()->getEndDate() || !$entry->getForm()->isActive()) {
            return new ViewModel(
                array(
                    'message'       => 'This form is currently closed.',
                    'specification' => $entry->getForm(),
                )
            );
        }

        $group = $this->_getGroup($formSpecification);

        $person = $this->getAuthentication()->getPersonObject();
        $form = new EditForm($this->getEntityManager(), $this->getLanguage(), $entry->getForm(), $entry, $person);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

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
                        $formEntry->addFieldEntry($fieldEntry);
                        $this->getEntityManager()->persist($fieldEntry);
                    }
                }

                $this->getEntityManager()->flush();

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

                $this->redirect()->toRoute(
                    'form_view',
                    array(
                        'action'   => 'view',
                        'id'       => $entry->getForm()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'specification' => $entry->getForm(),
                'form'          => $form,
                'group'         => $group,
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

        if ($person === null && !$entry->getForm()->isNonMember()) {
            $this->getResponse()->setStatusCode(404);
            return;
        } else if ($person !== null && $entry->getCreationPerson() != $person) {
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
}
