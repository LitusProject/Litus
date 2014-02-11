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

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Entry as FieldEntry,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\GuestInfo,
    FormBundle\Entity\Node\Entry as FormEntry,
    Zend\Mail\Message,
    Zend\Mail\Transport\TransportInterface as MailTransport;

/**
 * Doodle actions
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doodle
{
    static function save(FormEntry $formEntry = null, Person $person = null, GuestInfo $guestInfo = null, Form $formSpecification, $formData, EntityManager $entityManager, MailTransport $mailTransport = null)
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
            $formEntry = new FormEntry($person, $guestInfo, $formSpecification);
            $entityManager->persist($formEntry);
        } else {
            foreach($formEntry->getFieldEntries() as $fieldEntry) {
                $entityManager->remove($fieldEntry);
            }
            $entityManager->flush();
        }

        foreach ($formSpecification->getFields() as $field) {
            if (isset($formData['field-' . $field->getId()]) && $formData['field-' . $field->getId()]) {
                $fieldEntry = new FieldEntry($formEntry, $field, '1');
                $formEntry->addFieldEntry($fieldEntry);
                $entityManager->persist($fieldEntry);

                if (!$formSpecification->isMultiple())
                    break;
            }
        }

        $entityManager->flush();

        if ($formSpecification->hasMail() && isset($mailTransport)) {
            $mailAddress = $formSpecification->getMail()->getFrom();

            $mail = new Message();
            $mail->setBody($formSpecification->getCompletedMailBody($formEntry, $this->getLanguage()))
                ->setFrom($mailAddress)
                ->setSubject($formSpecification->getMail()->getSubject())
                ->addTo($formEntry->getPersonInfo()->getEmail(), $formEntry->getPersonInfo()->getFullName());

            if ($formSpecification->getMail()->getBcc())
                $mail->addBcc($mailAddress);

            if ('development' != getenv('APPLICATION_ENV'))
                $mailTransport->send($mail);
        }
    }
}
