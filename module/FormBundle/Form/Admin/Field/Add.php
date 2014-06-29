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

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    FormBundle\Component\Validator\Required as RequiredValidator,
    FormBundle\Component\Validator\StringField as StringFieldValidator,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\String as StringField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Form\Doodle,
    FormBundle\Entity\Field,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Field
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager
     */
    protected $_entityManager = null;

    /**
     * @var Form
     */
    protected $_form;

    /**
     * @param Form            $form
     * @param EntityManager   $entityManager
     * @param Field|null      $lastField
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(Form $form, EntityManager $entityManager, Field $lastField = null ,$name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_form = $form;

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach ($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('label_' . $language->getAbbrev());
            $field->setLabel('Label')
                ->setAttribute('class', 'field_label')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Select('type');
        $field->setLabel('Type')
            ->setRequired();
        $this->add($field);

        if ($form instanceof Doodle) {
            $field->setAttribute('options', array('timeslot' => 'Time Slot'));
        } else {
            $field->setAttribute('options', Field::$POSSIBLE_TYPES);
        }

        $field = new Text('order');
        $field->setLabel('Order')
            ->setAttribute('data-help', 'The display order of the fields, lower numbers are displayed first.')
            ->setRequired(True);
        $this->add($field);

        $field = new Checkbox('required');
        $field->setLabel('Required');
        $this->add($field);

        $string_form = new Collection('string_form');
        $string_form->setLabel('String Options')
            ->setAttribute('class', 'string_form extra_form hide');
        $this->add($string_form);

        $field = new Checkbox('multiline');
        $field->setLabel('Multiline')
            ->setAttribute('data-help', 'Allow multiple lines in the field (textarea).');
        $string_form->add($field);

        $field = new Text('charsperline');
        $field->setLabel('Max. characters per line (or Infinite)')
            ->setAttribute('data-help', 'The maximum numbers of characters on one line. Zero is infinite.');
        $string_form->add($field);

        $field = new Text('lines');
        $field->setLabel('Max. number of lines (Multiline fields only)')
            ->setAttribute('data-help', 'The maximum numbers of lines. Zero is infinite.');
        $string_form->add($field);

        $dropdown_form = new Collection('dropdown_form');
        $dropdown_form->setLabel('Options')
            ->setAttribute('class', 'dropdown_form extra_form hide');
        $this->add($dropdown_form);

        $dropdownTabs = new Tabs('dropdown_languages');
        $dropdown_form->add($dropdownTabs);

        $dropdownTabContent = new TabContent('dropdown_tab_content');

        foreach ($this->getLanguages() as $language) {
            $dropdownTabs->addTab(array($language->getName() => '#dropdown_tab_' . $language->getAbbrev()));

            $pane = new TabPane('dropdown_tab_' . $language->getAbbrev());

            $field = new Text('options_' . $language->getAbbrev());
            $field->setLabel('Options')
                ->setAttribute('data-help', 'The options comma separated.');
            $pane->add($field);

            $dropdownTabContent->add($pane);
        }

        $dropdown_form->add($dropdownTabContent);

        $string_form = new Collection('file_form');
        $string_form->setLabel('File Options')
            ->setAttribute('class', 'file_form extra_form hide');
        $this->add($string_form);

        $field = new Text('max_size');
        $field->setLabel('Max. size (in MB)')
            ->setValue(4);
        $string_form->add($field);

        $timeslot_form = new Collection('timeslot_form');
        $timeslot_form->setLabel('Time Slot Options')
            ->setAttribute('class', 'timeslot_form extra_form hide');
        $this->add($timeslot_form);

        $field = new Text('timeslot_start_date');
        $field->setLabel('Start Date')
            ->setRequired()
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true);
        $timeslot_form->add($field);

        $field = new Text('timeslot_end_date');
        $field->setLabel('End Date')
            ->setRequired()
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true);
        $timeslot_form->add($field);

        $timeslotTabs = new Tabs('timeslot_languages');
        $timeslot_form->add($timeslotTabs);

        $timeslotTabContent = new TabContent('timeslot_tab_content');

        foreach ($this->getLanguages() as $language) {
            $timeslotTabs->addTab(array($language->getName() => '#timeslot_tab_' . $language->getAbbrev()));

            $pane = new TabPane('timeslot_tab_' . $language->getAbbrev());

            $field = new Text('timeslot_location_' . $language->getAbbrev());
            $field->setLabel('Location');
            $pane->add($field);

            $field = new Textarea('timeslot_extra_info_' . $language->getAbbrev());
            $field->setLabel('Extra Information');
            $pane->add($field);

            $timeslotTabContent->add($pane);
        }

        $timeslot_form->add($timeslotTabContent);

        $visibility = new Collection('visibility');
        $visibility->setLabel('Visibility');
        $this->add($visibility);

        $field = new Select('visible_if');
        $field->setLabel('Visible If')
            ->setRequired()
            ->setAttribute('options', $this->_getVisibilityOptions());
        $visibility->add($field);

        $field = new Select('visible_value');
        $field->setLabel('Is')
            ->setRequired();
        $visibility->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'field_add');
        $this->add($field);

        $field = new Submit('submit_repeat');
        $field->setValue('Add And Repeat')
            ->setAttribute('class', 'field_add');
        $this->add($field);

        if(null !== $lastField)
            $this->populateFromField($lastField, true);
    }

    public function populateFromField(Field $field, $repeat = false)
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
            if ($field->isMultiLine())
                $data['lines'] = $field->getLines();
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
    }

    private function _getVisibilityOptions()
    {
        $options = array(0 => 'Always');
        foreach ($this->_form->getFields() as $field) {
            if ($field instanceof StringField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'string',
                    )
                );
            } elseif ($field instanceof DropdownField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'dropdown',
                        'data-values' => $field->getOptions(),
                    )
                );
            } elseif ($field instanceof CheckboxField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'checkbox',
                    )
                );
            } elseif ($field instanceof FileField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'file',
                    )
                );
            }
        }

        return $options;
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function getInputFilter()
    {
        $isTimeSlot = $this->_isTimeSlot();

        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach ($this->getLanguages() as $language) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'label_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault() && !$isTimeSlot,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'charsperline',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits'
                        ),
                        new StringFieldValidator(
                            isset($this->data['multiline']) ? $this->data['multiline'] : null,
                            isset($this->data['lines']) ? $this->data['lines'] : null
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'lines',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits'
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'max_size',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits'
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'order',
                    'required' => !$isTimeSlot,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'required',
                    'required' => false,
                    'validators' => array(
                        new RequiredValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'timeslot_start_date',
                    'required' => $isTimeSlot,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'timeslot_end_date',
                    'required' => $isTimeSlot,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => $isTimeSlot ? array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('timeslot_start_date', 'd/m/Y H:i'),
                    ) : array(),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'visible_if',
                    'required' => false,
                )
            )
        );

        return $inputFilter;
    }

    protected function _isTimeSlot()
    {
        return (isset($this->data['type']) && $this->data['type'] == 'timeslot');
    }
}
