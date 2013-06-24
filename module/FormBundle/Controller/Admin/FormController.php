<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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
    FormBundle\Entity\Nodes\Form,
    FormBundle\Entity\Nodes\Translation,
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
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Nodes\Form')
                ->findAllActive(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'entityManager' => $this->getEntityManager(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Nodes\Form')
                ->findAllOld(),
            $this->getParam('page')
        );

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
                $formData = $form->getFormData($formData);

                if ($formData['max'] == '')
                    $max = 0;
                else
                    $max = $formData['max'];

                $form = new Form(
                    $this->getAuthentication()->getPersonObject(),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['active'],
                    $max,
                    $formData['multiple'],
                    $formData['non_members'],
                    $formData['editable_by_user'],
                    $formData['mail'],
                    $formData['mail_subject'],
                    $formData['mail_body'],
                    $formData['mail_from'],
                    $formData['mail_bcc']
                );

                $this->getEntityManager()->persist($form);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()] && '' != $formData['submittext_' . $language->getAbbrev()]) {
                        $translation = new Translation(
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
                    ->setNonMember($formData['non_members'])
                    ->setMail($formData['mail'])
                    ->setMailSubject($formData['mail_subject'])
                    ->setMailBody($formData['mail_body'])
                    ->setMailFrom($formData['mail_from'])
                    ->setMailBcc($formData['mail_bcc']);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['introduction_' . $language->getAbbrev()] && '' != $formData['submittext_' . $language->getAbbrev()]) {
                        $translation = $formSpecification->getTranslation($language, false);

                        if ($translation === null) {
                            $translation = new Translation(
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
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
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

        // Delete all fields
        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($form);

        foreach ($fields as $field)
            $this->_deleteField($field);

        // Delete all entries
        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Nodes\Entry')
            ->findAllByForm($form);

        foreach ($entries as $entry)
            $this->getEntityManager()->remove($entry);

        // Delete all viewers
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
        // Delete all entered values
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
            ->getRepository('FormBundle\Entity\Nodes\Form')
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
