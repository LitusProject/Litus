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
    FormBundle\Form\SpecifiedForm\Add as AddForm,
    FormBundle\Form\SpecifiedForm\Edit as EditForm,
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

        $entriesCount = count($this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($formSpecification));

        if ($formSpecification->getMax() != 0 && $entriesCount >= $formSpecification->getMax()) {
            return new ViewModel(
                array(
                    'message'       => 'This form has reached the maximum number of submissions.',
                    'specification' => $formSpecification,
                )
            );
        }

        $person = $this->getAuthentication()->getPersonObject();

        if ($person === null && !$formSpecification->isNonMember()) {
            return new ViewModel(
                array(
                    'message'       => 'Please login to view this form.',
                    'specification' => $formSpecification,
                )
            );
        } else if ($person !== null) {
            $entries = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Nodes\Entry')
                ->findAllByFormAndPerson($formSpecification, $person);

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

                $formEntry = new FormEntry($person, $guestInfo, $formSpecification);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                foreach ($formSpecification->getFields() as $field) {
                    $value = $formData['field-' . $field->getId()];

                    $fieldEntry = new FieldEntry($formEntry, $field, $value);

                    $formEntry->addFieldEntry($fieldEntry);

                    $this->getEntityManager()->persist($fieldEntry);
                }

                $this->getEntityManager()->flush();

                if ($formSpecification->hasMail()) {
                    $mailAddress = $formSpecification->getMailFrom();

                    $mail = new Message();
                    $mail->setBody($formSpecification->getCompletedMailBody($this->getEntityManager(), $formEntry, $this->getLanguage()))
                        ->setFrom($mailAddress)
                        ->setSubject($formSpecification->getMailSubject())
                        ->addTo($formEntry->getPersonInfo()->getEmail(), $formEntry->getPersonInfo()->getFullName());

                    if ($formSpecification->getMailBcc())
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
            )
        );
    }

    public function editAction()
    {
        if (!($entry = $this->_getEntry()))
            return new ViewModel();

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
                    $mailAddress = $entry->getForm()->getMailFrom();

                    $mail = new Message();
                    $mail->setBody($entry->getForm()->getCompletedMailBody($this->getEntityManager(), $entry, $this->getLanguage()))
                        ->setFrom($mailAddress)
                        ->setSubject($entry->getForm()->getMailSubject())
                        ->addTo($entry->getPersonInfo()->getEmail(), $entry->getPersonInfo()->getFullName());

                    if ($formSpecification->getMailBcc())
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

        return $form;
    }

    private function _getEntry()
    {
        if (null === $this->getParam('id')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $entry = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Nodes\Entry')
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
}
