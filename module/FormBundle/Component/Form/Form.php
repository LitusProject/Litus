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

namespace FormBundle\Component\Form;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Entry as FieldEntry,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Node\Form as FormSpecification,
    FormBundle\Entity\Node\GuestInfo,
    FormBundle\Entity\Node\Entry as FormEntry,
    FormBundle\Form\SpecifiedForm\Add as AddForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Mail\Message,
    Zend\Mail\Transport\TransportInterface as MailTransport;

/**
 * Form actions
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Form
{
    public static function save(FormEntry $formEntry = null, Person $person = null, GuestInfo $guestInfo = null, FormSpecification $formSpecification, $formData, Language $language, AddForm $form, EntityManager $entityManager, MailTransport $mailTransport = null)
    {
        if ($person === null && $guestInfo == null) {
            $guestInfo = new GuestInfo(
                $entityManager,
                $formData['first_name'],
                $formData['last_name'],
                $formData['email']
            );
            $entityManager->persist($guestInfo);
        }

        if (null === $formEntry) {
            $formEntry = new FormEntry($person, $guestInfo, $formSpecification, isset($formData['save_as_draft']));
            $entityManager->persist($formEntry);
        }

        if ($formEntry->isGuestEntry()) {
            $formEntry->getGuestInfo()
                ->setFirstName($formData['first_name'])
                ->setLastName($formData['last_name'])
                ->setEmail($formData['email']);
        }

        $formEntry->setDraft(isset($formData['save_as_draft']));

        foreach ($formSpecification->getFields() as $field) {
            $value = $formData['field-' . $field->getId()];

            $fieldEntry = $entityManager
                ->getRepository('FormBundle\Entity\Entry')
                ->findOneByFormEntryAndField($formEntry, $field);
            $removed = false;

            if ($field instanceof FileField) {
                $value = '';
                $filePath = $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('form.file_upload_path');

                if (isset($formData['field-' . $field->getId() . '-removed'])) {
                    $removed = true;

                    if (isset($fieldEntry)) {
                        if (file_exists($filePath . '/' . $fieldEntry->getValue()))
                            unlink($filePath . '/' . $fieldEntry->getValue());

                        $entityManager->remove($fieldEntry);
                    }
                } else {
                    $upload = new FileUpload();
                    $upload->setValidators($form->getInputFilter()->get('field-' . $field->getId())->getValidatorChain()->getValidators());
                    if ($upload->isValid('field-' . $field->getId())) {
                        if (null === $fieldEntry || $fieldEntry->getValue() == '') {
                            $fileName = '';
                            do {
                                $fileName = sha1(uniqid());
                            } while (file_exists($filePath . '/' . $fileName));
                        } else {
                            $fileName = $fieldEntry->getValue();
                            if (file_exists($filePath . '/' . $fileName))
                                unlink($filePath . '/' . $fileName);
                        }

                        $upload->addFilter('Rename', $filePath . '/' . $fileName, 'field-' . $field->getId());
                        $upload->receive('field-' . $field->getId());

                        $value = $fileName;
                    }

                    $errors = $upload->getMessages();

                    if (!$field->isRequired() && isset($errors['fileUploadErrorNoFile']))
                        unset($errors['fileUploadErrorNoFile']);

                    if (sizeof($errors) > 0) {
                        $form->setMessages(array('field-' . $field->getId() => $errors));

                        return false;
                    } elseif ($value == '' && null !== $fieldEntry) {
                        $value = $fieldEntry->getValue();
                    }
                }
            }

            if (!$removed) {
                if ($fieldEntry) {
                    $fieldEntry->setValue($value);
                } else {
                    $fieldEntry = new FieldEntry($formEntry, $field, $value);
                    $formEntry->addFieldEntry($fieldEntry);
                    $entityManager->persist($fieldEntry);
                }
            }
        }

        $entityManager->flush();

        if (!isset($formData['save_as_draft'])) {
            if ($formSpecification->hasMail() && isset($mailTransport)) {
                $mailAddress = $formSpecification->getMail()->getFrom();

                $mail = new Message();
                $mail->setBody($formSpecification->getCompletedMailBody($formEntry, $language))
                    ->setFrom($mailAddress)
                    ->setSubject($formSpecification->getMail()->getSubject())
                    ->addTo($formEntry->getPersonInfo()->getEmail(), $formEntry->getPersonInfo()->getFullName());

                if ($formSpecification->getMail()->getBcc())
                    $mail->addBcc($mailAddress);

                if ('development' != getenv('APPLICATION_ENV'))
                    $mailTransport->send($mail);
            }
        }

        return true;
    }
}
