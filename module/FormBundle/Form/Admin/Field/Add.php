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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Admin\Field;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use FormBundle\Entity\Field;
use FormBundle\Entity\Field\Checkbox as CheckboxFieldEntity;
use FormBundle\Entity\Field\Dropdown as DropdownFieldEntity;
use FormBundle\Entity\Field\File as FileFieldEntity;
use FormBundle\Entity\Field\Text as StringFieldEntity;
use FormBundle\Entity\Field\TimeSlot as TimeSlotFieldEntity;
use FormBundle\Entity\Node\Form;
use FormBundle\Entity\Node\Form\Doodle;

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
    protected $form;

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var boolean
     */
    protected $repeat;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'form_type',
                    'options' => $this->form instanceof Doodle ? array('timeslot' => 'Time Slot') : Field::$possibleTypes,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'order',
                'label'      => 'Order',
                'required'   => true,
                'attributes' => array(
                    'id'        => 'order',
                    'data-help' => 'The display order of the fields, lower numbers are displayed first.',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'required',
                'label'      => 'Required',
                'attributes' => array(
                    'id' => 'required',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'FieldRequired'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_text',
                'name'       => 'text_form',
                'label'      => 'String Options',
                'attributes' => array(
                    'class' => 'string_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_dropdown',
                'name'       => 'dropdown_form',
                'label'      => 'Options',
                'attributes' => array(
                    'class' => 'dropdown_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_file',
                'name'       => 'file_form',
                'label'      => 'File Options',
                'attributes' => array(
                    'class' => 'file_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_timeSlot',
                'name'       => 'timeslot_form',
                'label'      => 'Time Slot Options',
                'attributes' => array(
                    'class' => 'timeslot_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'visibility',
                'label'      => 'Visibility',
                'attributes' => array(
                    'id' => 'visibility',
                ),
                'elements' => array(
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
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_text',
                'name'       => 'text_form',
                'label'      => 'String Options',
                'attributes' => array(
                    'class' => 'string_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_dropdown',
                'name'       => 'dropdown_form',
                'label'      => 'Options',
                'attributes' => array(
                    'class' => 'dropdown_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_file',
                'name'       => 'file_form',
                'label'      => 'File Options',
                'attributes' => array(
                    'class' => 'file_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_field_field_timeSlot',
                'name'       => 'timeslot_form',
                'label'      => 'Time Slot Options',
                'attributes' => array(
                    'class' => 'timeslot_form extra_form hide',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'visibility',
                'label'      => 'Visibility',
                'attributes' => array(
                    'id' => 'visibility',
                ),
                'elements' => array(
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
            )
        );

        $this->addSubmit('Add', 'field_add');
        $this->addSubmit('Add And Repeat', 'field_add', 'submit_repeat');

        if ($this->field !== null) {
            if ($this->repeat) {
                $field = clone $this->field;
                if ($field instanceof TimeSlotFieldEntity) {
                    $interval = $field->getStartDate()->diff($field->getEndDate());
                    $startDate = $field->getStartDate();
                    $endDate = $field->getEndDate();
                    $startDate->add($interval);
                    $endDate->add($interval);
                }
                $hydrator = $this->getHydrator();
                $this->populateValues($hydrator->extract($field));
            } else {
                $hydrator = $this->getHydrator();
                $this->populateValues($hydrator->extract($this->field));
            }
        }
    }

    /**
     * @param  FieldsetInterface $container
     * @param  Language          $language
     * @param  boolean           $isDefault
     * @return null
     */
    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'       => 'text',
                'name'       => 'label',
                'label'      => 'Label',
                'required'   => $isDefault,
                'attributes' => array(
                    'class' => 'field_label',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * @return array
     */
    protected function getVisibilityOptions()
    {
        $options = array('always' => 'Always');
        foreach ($this->form->getFields() as $field) {
            if ($this->field !== null && $field->getId() == $this->field->getId()) {
                continue;
            }

            if ($field instanceof StringFieldEntity) {
                $options[] = array(
                    'label'      => $field->getLabel(),
                    'value'      => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'string',
                    ),
                );
            } elseif ($field instanceof DropdownFieldEntity) {
                $options[] = array(
                    'label'      => $field->getLabel(),
                    'value'      => $field->getId(),
                    'attributes' => array(
                        'data-type'   => 'dropdown',
                        'data-values' => $field->getOptions(),
                    ),
                );
            } elseif ($field instanceof CheckboxFieldEntity) {
                $options[] = array(
                    'label'      => $field->getLabel(),
                    'value'      => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'checkbox',
                    ),
                );
            } elseif ($field instanceof FileFieldEntity) {
                $options[] = array(
                    'label'      => $field->getLabel(),
                    'value'      => $field->getId(),
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
        $this->form = $form;

        return $this;
    }

    /**
     * @param  Field|null $field
     * @return self
     */
    public function setField(Field $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @param  boolean $repeat
     * @return self
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $type = $this->getType();

        if ($type == 'string') {
            $stringForm = $this->get('text_form');
            $stringForm->setRequired();
        } elseif ($type == 'dropdown') {
            $dropdownForm = $this->get('dropdown_form');
            $dropdownForm->setRequired();
        } elseif ($type == 'file') {
            $fileForm = $this->get('file_form');
            $fileForm->setRequired();
        } elseif ($type == 'timeslot') {
            $timeSlotForm = $this->get('timeslot_form');
            $timeSlotForm->setRequired();
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

    /**
     * @return string|null
     */
    private function getType()
    {
        if ($this->field === null) {
            return $this->data['type'];
        }

        if ($this->field instanceof StringFieldEntity) {
            return 'string';
        } elseif ($this->field instanceof DropdownFieldEntity) {
            return 'dropdown';
        } elseif ($this->field instanceof CheckboxFieldEntity) {
            return 'checkbox';
        } elseif ($this->field instanceof FileFieldEntity) {
            return 'file';
        } elseif ($this->field instanceof TimeSlotFieldEntity) {
            return 'timeslot';
        }
    }
}
