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
    protected $hydrator = 'FormBundle\Hydrator\Field';

    /**
    * @var Form
    */
    protected $_form;

    /**
    * @var Field
    */
    protected $_field;

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
            'type'       => 'form_field_field_string',
            'name'       => 'string_form',
            'label'      => 'String Options',
            'attributes' => array(
                'class' => 'string_form extra_form hide',
            ),
        ));

        $this->add(array(
            'type'       => 'form_field_field_dropdown',
            'name'       => 'dropdown_form',
            'label'      => 'Options',
            'attributes' => array(
                'class' => 'dropdown_form extra_form hide',
            ),
        ));

        $this->add(array(
            'type'      => 'form_field_field_file',
            'name'       => 'file_form',
            'label'      => 'File Options',
            'attributes' => array(
                'class' => 'file_form extra_form hide',
            ),
        ));

        $this->add(array(
            'type'       => 'form_field_field_timeslot',
            'name'       => 'timeslot_form',
            'label'      => 'Timeslot Options',
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
                    'name'       => 'if',
                    'label'      => 'Visible If',
                    'required'   => true,
                    'attributes' => array(
                        'id'      => 'visible_if',
                        'options' => $this->getVisibilityOptions(),
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'value',
                    'label'      => 'Is',
                    'required'   => true,
                    'attributes' => array(
                        'id' => 'visible_value',
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'field_add');
        $this->addSubmit('Add And Repeat', 'field_add', 'submit_repeat');

        if (null !== $this->_field) {
            $this->bind($this->_field);
        }
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
        $options = array('always' => 'Always');
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
    public function setField(Field $field = null)
    {
        $this->_field = $field;

        return $this;
    }

    public function getInputFilterSpecification()
    {
        $type = $this->_getType();

        if ($type == 'string') {
            $this->get('string_form')->setRequired();
        } elseif ($type == 'dropdown') {
            $this->get('dropdown_form')->setRequired();
        } elseif ($type == 'file') {
            $this->get('file_form')->setRequired();
        } elseif ($type == 'timeslot') {
            $this->get('timeslot_form')->setRequired();
        }

        $specs = parent::getInputFilterSpecification();

        if ($type == 'timeslot') {
            $specs['order']['required'] = false;
            foreach ($this->getLanguages() as $language) {
                $specs['tab_content']['tab_' . $language->getAbbrev()]['label']['required'] = false;
            }
            $specs['visibility']['if']['required'] = false;
            $specs['visibility']['value']['required'] = false;
        }

        if ($this->data['visibility']['if'] == 'always') {
            $specs['visibility']['value']['required'] = false;
        }

        return $specs;
    }

    private function _getType()
    {
        if (null === $this->_field) {
            return $this->data['type'];
        }

        if ($this->_field instanceof StringFieldEntity) {
            return 'string';
        } elseif ($this->_field instanceof DropdownFieldEntity) {
            return 'dropdown';
        } elseif ($this->_field instanceof CheckboxFieldEntity) {
            return 'checkbox';
        } elseif ($this->_field instanceof FileFieldEntity) {
            return 'file';
        } elseif ($this->_field instanceof TimeslotFieldEntity) {
            return 'timeslot';
        }
    }
}
