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

namespace FormBundle\Form\Admin\Field;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Entity\General\Language,
    FormBundle\Component\Validator\Required as RequiredValidator,
    FormBundle\Entity\Field,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Field\String as StringField,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Form\Doodle;

/**
 * Add Field
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
    * @var Form
    */
    private $_form;

    /**
    * @var Field
    */
    private $_field;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'required'   => true,
            'attributes' => array(
                'id'      => 'form_type',
                'options' => $this->_form instanceof Doodle ? array('timeslot' => 'Time Slot') : Field::$POSSIBLE_TYPES,
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'order',
            'label'      => 'Order',
            'required'   => true,
            'attributes' => array(
                'id'        => 'order',
                'data-help' => 'The display order of the fields, lower numbers are displayed first.',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'required',
            'label'      => 'Required',
            'attributes' => array(
                'id' => 'required',
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        new RequiredValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type' => 'form_field_field_string',
            'name' => 'string_form',
            'label' => 'String Options',
            'attributes' => array(
                'class' => 'string_form extra_form hide',
            ),
        ));

        $this->add(array(
            'type' => 'form_field_field_dropdown',
            'name' => 'dropdown_form',
            'label' => 'Options',
            'attributes' => array(
                'class' => 'dropdown_form extra_form hide',
            ),
        ));

        $this->add(array(
            'type' => 'form_field_field_file',
            'name' => 'file_form',
            'label' => 'File Options',
            'attributes' => array(
                'class' => 'file_form extra_form hide',
            ),
        ));

        $this->add(array(
            'type' => 'form_field_field_timeslot',
            'name' => 'timeslot_form',
            'label' => 'Timeslot Options',
            'attributes' => array(
                'class' => 'timeslot_form extra_form hide',
            ),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'visibility',
            'label'      => 'Visibility',
            'attributes' => array(
                'id' => 'visibility',
            ),
            'elements'   => array(
                array(
                    'type'       => 'select',
                    'name'       => 'visible_if',
                    'label'      => 'Visible If',
                    'required'   => true,
                    'attributes' => array(
                        'options' => $this->getVisibilityOptions(),
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'visible_value',
                    'label'      => 'Is',
                    'required'   => true,
                ),
            ),
        ));

        $this->addSubmit('Add', 'field_add');
        $this->addSubmit('Add And Repeat', 'field_add', 'submit_repeat');
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'       => 'text',
            'name'       => 'label',
            'label'      => 'Label',
            'required'   => $isDefault,
            'attributes' => array(
                'class' => 'field_label',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }

    protected function getVisibilityOptions()
    {
        $options = array(0 => 'Always');
        foreach ($this->_form->getFields() as $field) {
            if (null !== $this->_field && $field->getId() == $this->_field->getId()) {
                continue;
            }

            if ($field instanceof StringField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'string',
                    ),
                );
            } elseif ($field instanceof DropdownField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'dropdown',
                        'data-values' => $field->getOptions(),
                    ),
                );
            } elseif ($field instanceof CheckboxField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'checkbox',
                    ),
                );
            } elseif ($field instanceof FileField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'file',
                    ),
                );
            }
        }

        return $options;
    }

    /**
    * @param  Form $form
    * @return self
    */
    public function setForm(Form $form)
    {
        $this->_form = $form;

        return $this;
    }

    /**
    * @param  Field $field
    * @return self
    */
    public function setField(Field $field)
    {
        $this->_field = $field;

        return $this;
    }

    /*public function populateFromField(Field $field, $repeat = false)
    {
        $data = array(
            'order'    => $field->getOrder(),
            'required' => $field->isRequired(),
        );

        if ($field instanceof StringField) {
            $data['type'] = 'string';
        } elseif ($field instanceof DropdownField) {
            $data['type'] = 'dropdown';
        } elseif ($field instanceof CheckboxField) {
            $data['type'] = 'checkbox';
        } elseif ($field instanceof FileField) {
            $data['type'] = 'file';
        } elseif ($field instanceof TimeSlotField) {
            $data['type'] = 'timeslot';
        }

        if ($field instanceof StringField) {
            $data['charsperline'] = $field->getLineLength();
            $data['multiline'] = $field->isMultiLine();
            if ($field->isMultiLine()) {
                $data['lines'] = $field->getLines();
            }
        } elseif ($field instanceof FileField) {
            $data['max_size'] = $field->getMaxSize();
        } elseif ($field instanceof TimeSlotField) {
            if ($repeat) {
                $interval = $field->getStartDate()->diff($field->getEndDate());
                $startDate = clone $field->getStartDate();
                $endDate = clone $field->getEndDate();
                $startDate->add($interval);
                $endDate->add($interval);
            } else {
                $startDate = $field->getStartDate();
                $endDate = $field->getEndDate();
            }
            $data['timeslot_start_date'] = $startDate->format('d/m/Y H:i');
            $data['timeslot_end_date'] = $endDate->format('d/m/Y H:i');
        }

        foreach ($this->getLanguages() as $language) {
            $data['label_' . $language->getAbbrev()] = $field->getLabel($language, false);

            if ($field instanceof DropdownField) {
                $data['options_' . $language->getAbbrev()] = $field->getOptions($language, false);
            } elseif ($field instanceof TimeSlotField) {
                $data['timeslot_location_' . $language->getAbbrev()] = $field->getLocation($language, false);
                $data['timeslot_extra_info_' . $language->getAbbrev()] = $field->getExtraInformation($language, false);
            }
        }

        if (null !== $field->getVisibilityDecissionField()) {
            $data['visible_if'] = $field->getVisibilityDecissionField()->getId();
            $data['visible_value'] = $field->getVisibilityValue();
            $this->get('visibility')->get('visible_value')->setAttribute('data-current_value', $field->getVisibilityValue());
        }

        $this->setData($data);
    }*/
}
