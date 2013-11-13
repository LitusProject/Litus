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

namespace FormBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    FormBundle\Entity\Mail\Mail,
    FormBundle\Entity\Mail\Translation as MailTranslation,
    FormBundle\Entity\Node\Form\Doodle,
    FormBundle\Entity\Node\Form\Form,
    FormBundle\Entity\Node\Translation\Form as FormTranslation,
    FormBundle\Entity\ViewerMap,
    FormBundle\Form\Admin\Form\Add as AddForm,
    FormBundle\Form\Admin\Form\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * FormController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FormController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        foreach($paginator as $form)
            $form->setEntityManager($this->getEntityManager());

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        foreach($paginator as $form)
            $form->setEntityManager($this->getEntityManager());

        return new ViewModel(
            array(
                'entityManager' => $this->getEntityManager(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $formData = $form->getFormData($formData);

                if ($formData['max'] == '')
                    $max = 0;
                else
                    $max = $formData['max'];

                if ($formData['type'] == 'doodle') {
                    $form = new Doodle(
                        $this->getAuthentication()->getPersonObject(),
                        DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                        DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                        $formData['active'],
                        $formData['multiple'],
                        $formData['non_members'],
                        $formData['editable_by_user'],
                        $formData['names_visible_for_others']
                    );

                    if ($formData['reminder_mail']) {
                        $mail = new Mail($formData['reminder_mail_from'], $formData['reminder_mail_bcc']);
                        $this->getEntityManager()->persist($mail);

                        foreach($languages as $language) {
                            $translation = new MailTranslation(
                                $mail,
                                $language,
                                $formData['reminder_mail_subject_' . $language->getAbbrev()],
                                $formData['reminder_mail_body_' . $language->getAbbrev()]
                            );
                            $this->getEntityManager()->persist($translation);
                        }
                        $form->setReminderMail($mail);
                    }
                } else {
                    $form = new Form(
                        $this->getAuthentication()->getPersonObject(),
                        DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                        DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                        $formData['active'],
                        $max,
                        $formData['multiple'],
                        $formData['non_members'],
                        $formData['editable_by_user']
                    );
                }

                $this->getEntityManager()->persist($form);

                if ($formData['mail']) {
                    $mail = new Mail($formData['mail_from'], $formData['mail_bcc']);
                    $this->getEntityManager()->persist($mail);
                    foreach($languages as $language) {
                        $translation = new MailTranslation(
                            $mail,
                            $language,
                            $formData['mail_subject_' . $language->getAbbrev()],
                            $formData['mail_body_' . $language->getAbbrev()]
                        );
                        $this->getEntityManager()->persist($translation);
                    }
                    $form->setMail($mail);
                }

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()] && '' != $formData['submittext_' . $language->getAbbrev()]) {
                        $translation = new FormTranslation(
                            $form,
                            $language,
                            $formData['title_' . $language->getAbbrev()],
                            $formData['introduction_' . $language->getAbbrev()],
                            $formData['submittext_' . $language->getAbbrev()],
                            $formData['updatetext_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $map = new ViewerMap($form, $this->getAuthentication()->getPersonObject(), true, true);

                $this->getEntityManager()->persist($map);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The form was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_form',
                    array(
                        'action' => 'manage'
                    )
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

    public function editAction()
    {
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        $formSpecification->setEntityManager($this->getEntityManager());

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($formSpecification);

        if (!$formSpecification->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this form!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $form = new EditForm($this->getEntityManager(), $formSpecification);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $formData = $form->getFormData($formData);

                if ($formData['max'] == '')
                    $max = 0;
                else
                    $max = $formData['max'];

                $formSpecification->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']))
                    ->setActive($formData['active'])
                    ->setMax($max)
                    ->setMultiple($formData['multiple'])
                    ->setEditableByUser($formData['editable_by_user'])
                    ->setNonMember($formData['non_members']);

                if ($formSpecification instanceOf Doodle) {
                    $formSpecification->setNamesVisibleForOthers($formData['names_visible_for_others']);

                    if ($formData['reminder_mail']) {
                        $mail = $formSpecification->getReminderMail();

                        if (null === $mail) {
                            $mail = new Mail($formData['reminder_mail_from'], $formData['reminder_mail_bcc']);
                            $this->getEntityManager()->persist($mail);
                        } else {
                            $mail->setFrom($formData['reminder_mail_from'])
                                ->setBcc($formData['reminder_mail_bcc']);
                        }

                        foreach($languages as $language) {
                            $translation = $mail->getTranslation($language, false);

                            if (null === $translation) {
                                $translation = new MailTranslation(
                                    $mail,
                                    $language,
                                    $formData['reminder_mail_subject_' . $language->getAbbrev()],
                                    $formData['reminder_mail_body_' . $language->getAbbrev()]
                                );
                                $this->getEntityManager()->persist($translation);
                            } else {
                                $translation->setSubject($formData['reminder_mail_subject_' . $language->getAbbrev()])
                                    ->setContent($formData['reminder_mail_body_' . $language->getAbbrev()]);
                            }
                        }
                        $formSpecification->setReminderMail($mail);
                    } else {
                        $formSpecification->setReminderMail(null);
                    }
                }

                if ($formData['mail']) {
                    $mail = $formSpecification->getMail();

                    if (null === $mail) {
                        $mail = new Mail($formData['mail_from'], $formData['mail_bcc']);
                        $this->getEntityManager()->persist($mail);
                    } else {
                        $mail->setFrom($formData['mail_from'])
                            ->setBcc($formData['mail_bcc']);
                    }

                    foreach($languages as $language) {
                        $translation = $mail->getTranslation($language, false);

                        if (null === $translation) {
                            $translation = new MailTranslation(
                                $mail,
                                $language,
                                $formData['mail_subject_' . $language->getAbbrev()],
                                $formData['mail_body_' . $language->getAbbrev()]
                            );
                            $this->getEntityManager()->persist($translation);
                        } else {
                            $translation->setSubject($formData['mail_subject_' . $language->getAbbrev()])
                                ->setContent($formData['mail_body_' . $language->getAbbrev()]);
                        }
                    }
                    $formSpecification->setMail($mail);
                } else {
                    $formSpecification->setMail(null);
                }

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()] && '' != $formData['submittext_' . $language->getAbbrev()]) {
                        $translation = $formSpecification->getTranslation($language, false);

                        if (null === $translation) {
                            $translation = new FormTranslation(
                                $formSpecification,
                                $language,
                                $formData['title_' . $language->getAbbrev()],
                                $formData['introduction_' . $language->getAbbrev()],
                                $formData['submittext_' . $language->getAbbrev()],
                                $formData['updatetext_' . $language->getAbbrev()]
                            );
                        } else {
                            $translation->setTitle($formData['title_' . $language->getAbbrev()])
                                ->setIntroduction($formData['introduction_' . $language->getAbbrev()])
                                ->setSubmitText($formData['submittext_' . $language->getAbbrev()])
                                ->setUpdateText($formData['updatetext_' . $language->getAbbrev()]);
                        }

                        $this->getEntityManager()->persist($translation);
                    } else {
                        $translation = $formSpecification->getTranslation($language, false);

                        if ($translation !== null) {
                            $this->getEntityManager()->remove($translation);
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The form was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_form',
                    array(
                        'action' => 'edit',
                        'id' => $formSpecification->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'group' => $group,
                'form' => $form,
                'formSpecification' => $formSpecification,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($form = $this->_getForm()))
            return new ViewModel();

        if (!$form->canBeEditedBy($this->getAuthentication()->getPersonObject())) {

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this form!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($form);

        foreach ($fields as $field)
            $this->_deleteField($field);

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($form);

        foreach ($entries as $entry)
            $this->getEntityManager()->remove($entry);

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllByForm($form);

        foreach ($viewers as $viewer)
            $this->getEntityManager()->remove($viewer);

        $this->getEntityManager()->remove($form);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _deleteField($field)
    {
        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($field);

        foreach ($entries as $entry)
            $this->getEntityManager()->remove($entry);

        $this->getEntityManager()->remove($field);
    }

    private function _getForm()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the form!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $form = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findOneById($this->getParam('id'));

        if (null === $form) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No form with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $form;
    }
}
