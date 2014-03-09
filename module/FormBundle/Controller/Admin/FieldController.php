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

namespace FormBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\String as StringField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField,
    FormBundle\Entity\Field\Translation\Option as OptionTranslationField,
    FormBundle\Entity\Field\Translation\TimeSlot as TimeSlotTranslationField,
    FormBundle\Entity\Translation,
    FormBundle\Form\Admin\Field\Add as AddForm,
    FormBundle\Form\Admin\Field\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * FieldController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
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

        $fields = $formSpecification->getFields();

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'fields' => $fields,
            )
        );
    }

    public function addAction()
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

        $latestField = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findLatestField($formSpecification);

        $form = new AddForm($formSpecification, $this->getEntityManager(), $this->getParam('repeat') ? $latestField : null);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $visibilityDecissionField = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Field')
                    ->findOneById($formData['visible_if']);

                switch ($formData['type']) {
                    case 'string':
                        $field = new StringField(
                            $formSpecification,
                            $formData['order'],
                            $formData['required'],
                            $visibilityDecissionField,
                            isset($visibilityDecissionField) ? $formData['visible_value'] : null,
                            $formData['charsperline'] === '' ? 0 : $formData['charsperline'],
                            $formData['lines'] === '' ? 0 : $formData['lines'],
                            $formData['multiline']
                        );
                        break;
                    case 'dropdown':
                        $field = new DropdownField(
                            $formSpecification,
                            $formData['order'],
                            $formData['required'],
                            $visibilityDecissionField,
                            isset($visibilityDecissionField) ? $formData['visible_value'] : null
                        );

                        foreach ($languages as $language) {
                            if ('' != $formData['options_' . $language->getAbbrev()]) {
                                $translation = new OptionTranslationField(
                                    $field,
                                    $language,
                                    $formData['options_' . $language->getAbbrev()]
                                );

                                $this->getEntityManager()->persist($translation);
                            }
                        }

                        break;
                    case 'checkbox':
                        $field = new CheckboxField(
                            $formSpecification,
                            $formData['order'],
                            $formData['required'],
                            $visibilityDecissionField,
                            isset($visibilityDecissionField) ? $formData['visible_value'] : null
                        );
                        break;
                     case 'file':
                        $field = new FileField(
                            $formSpecification,
                            $formData['order'],
                            $formData['required'],
                            $visibilityDecissionField,
                            isset($visibilityDecissionField) ? $formData['visible_value'] : null,
                            $formData['max_size'] === '' ? 4 : $formData['max_size']
                        );
                        break;
                    case 'timeslot':
                        $field = new TimeSlotField(
                            $formSpecification,
                            0,
                            $formData['required'],
                            $visibilityDecissionField,
                            isset($visibilityDecissionField) ? $formData['visible_value'] : null,
                            DateTime::createFromFormat('d#m#Y H#i', $formData['timeslot_start_date']),
                            DateTime::createFromFormat('d#m#Y H#i', $formData['timeslot_end_date'])
                        );

                        foreach ($languages as $language) {
                            if ('' == $formData['timeslot_location_' . $language->getAbbrev()] && '' == $formData['timeslot_extra_info_' . $language->getAbbrev()])
                                continue;
                            $translation = new TimeSlotTranslationField(
                                $field,
                                $language,
                                $formData['timeslot_location_' . $language->getAbbrev()],
                                $formData['timeslot_extra_info_' . $language->getAbbrev()]
                            );

                            $this->getEntityManager()->persist($translation);
                        }
                        break;
                    default:
                        throw new UnsupportedTypeException('This field type is unknown!');
                }

                $formSpecification->addField($field);

                $this->getEntityManager()->persist($field);

                foreach ($languages as $language) {
                    if ('' != $formData['label_' . $language->getAbbrev()]) {
                        $translation = new Translation(
                            $field,
                            $language,
                            $formData['label_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The field was successfully created!'
                    )
                );

                if (isset($formData['submit_repeat'])) {
                    $this->redirect()->toRoute(
                        'form_admin_form_field',
                        array(
                            'action' => 'add',
                            'id' => $formSpecification->getId(),
                            'repeat' => 1,
                        )
                    );
                } else {
                    $this->redirect()->toRoute(
                        'form_admin_form_field',
                        array(
                            'action' => 'manage',
                            'id' => $formSpecification->getId(),
                        )
                    );
                }

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($field = $this->_getField()))
            return new ViewModel();

        if (!$field->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
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

        $form = new EditForm($field, $this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                $visibilityDecissionField = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Field')
                    ->findOneById($formData['visible_if']);

                $field->setOrder($formData['order'])
                    ->setRequired($formData['required'])
                    ->setVisibilityDecissionField($visibilityDecissionField)
                    ->setVisibilityValue(isset($visibilityDecissionField) ? $formData['visible_value'] : null);

                if ($field instanceof StringField) {
                    $field->setLineLength($formData['charsperline'] === '' ? 0 : $formData['charsperline'])
                        ->setLines($formData['lines'] === '' ? 0 : $formData['lines'])
                        ->setMultiLine($formData['multiline']);
                } elseif ($field instanceof DropdownField) {
                    foreach ($languages as $language) {
                        if ('' != $formData['options_' . $language->getAbbrev()]) {
                            $translation = $field->getOptionTranslation($language, false);

                            if (null !== $translation) {
                                $translation->setOptions($formData['options_' . $language->getAbbrev()]);
                            } else {
                                $translation = new OptionTranslationField(
                                    $field,
                                    $language,
                                    $formData['options_' . $language->getAbbrev()]
                                );

                                $this->getEntityManager()->persist($translation);
                            }
                        }
                    }
                } elseif ($field instanceof FileField) {
                    $field->setMaxSize($formData['max_size'] === '' ? 4 : $formData['max_size']);
                } elseif ($field instanceof TimeSlotField) {
                    $field->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['timeslot_start_date']))
                        ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['timeslot_end_date']));

                    foreach ($languages as $language) {
                        $translation = $field->getTimeSlotTranslation($language, false);

                        if ('' == $formData['timeslot_location_' . $language->getAbbrev()] && '' == $formData['timeslot_extra_info_' . $language->getAbbrev()]) {
                            if (null !== $translation)
                                $this->getEntityManager()->remove($translation);
                            continue;
                        }

                        if (null !== $translation) {
                            $translation->setLocation($formData['timeslot_location_' . $language->getAbbrev()])
                                ->setExtraInformation($formData['timeslot_extra_info_' . $language->getAbbrev()]);
                        } else {
                            $translation = new TimeSlotTranslationField(
                                $field,
                                $language,
                                $formData['timeslot_location_' . $language->getAbbrev()],
                                $formData['timeslot_extra_info_' . $language->getAbbrev()]
                            );

                            $this->getEntityManager()->persist($translation);
                        }
                    }
                }

                foreach ($languages as $language) {
                    if ('' != $formData['label_' . $language->getAbbrev()]) {
                        $translation = $field->getTranslation($language, false);

                        if (null !== $translation) {
                            $translation->setLabel($formData['label_' . $language->getAbbrev()]);
                        } else {
                            $translation = new Translation(
                                $field,
                                $language,
                                $formData['label_' . $language->getAbbrev()]
                            );

                            $this->getEntityManager()->persist($translation);
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The field was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'form_admin_form_field',
                    array(
                        'action' => 'manage',
                        'id' => $field->getForm()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'formSpecification' => $field->getForm(),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($field = $this->_getField()))
            return new ViewModel();

        if (!$field->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {

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

        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($field);

        foreach ($entries as $entry)
            $this->getEntityManager()->remove($entry);

        $this->getEntityManager()->remove($field);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function sortAction()
    {
        $this->initAjax();

        if(!($formSpecification = $this->_getForm()))

            return new ViewModel();

        if(!$this->getRequest()->isPost())

            return new ViewModel();

        $data = $this->getRequest()->getPost();

        if(!$data['items'])

            return new ViewModel();

        foreach ($data['items'] as $order => $id) {
            $field = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Field')
                ->findOneById($id);
            $field->setOrder($order+1);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                )
            )
        );
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

        $formSpecification = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findOneById($this->getParam('id'));

        if (null === $formSpecification) {
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

        return $formSpecification;
    }

    private function _getField()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the field!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $field = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findOneById($this->getParam('id'));

        if (null === $field) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No field with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $field;
    }
}
