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

namespace FormBundle\Form\Admin\Field;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\String as StringField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField,
    FormBundle\Entity\Field,
    Zend\Form\Element\Submit;

/**
 * Edit Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \FormBundle\Entity\Field
     */
    private $_field;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \FormBundle\Entity\Field $field The field we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Field $fieldSpecification, EntityManager $entityManager, $name = null)
    {
        parent::__construct($fieldSpecification->getForm(), $entityManager, $name);

        $this->_field = $fieldSpecification;

        $this->get('type')->setAttribute('disabled', 'disabled');
        $this->get('visibility')->get('visible_if')->setAttribute('options', $this->_getVisibilityOptions());

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'field_edit');
        $this->add($field);

        $this->_populateFromField($fieldSpecification);
    }

    private function _getVisibilityOptions()
    {
        $options = array(0 => 'Always');
        foreach($this->_form->getFields() as $field) {
            if ($field == $this->_field)
                continue;

            if ($field instanceof StringField) {
                $options[] = array(
                    'label' => $field->getLabel(),
                    'value' => $field->getId(),
                    'attributes' => array(
                        'data-type' => 'string',
                    )
                );
            } else if ($field instanceof DropdownField) {
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

    private function _populateFromField(Field $field)
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
            $data['timeslot_start_date'] = $field->getStartDate()->format('d/m/Y H:i');
            $data['timeslot_end_date'] = $field->getEndDate()->format('d/m/Y H:i');
            $data['timeslot_location'] = $field->getLocation();
            $data['timeslot_extra_info'] = $field->getExtraInformation();
        }

        foreach($this->getLanguages() as $language) {
            $data['label_' . $language->getAbbrev()] = $field->getLabel($language, false);

            if($field instanceof DropdownField) {
                $data['options_' . $language->getAbbrev()] = $field->getOptions($language, false);
            }
        }

        if (null !== $field->getVisibilityDecissionField()) {
            $data['visible_if'] = $field->getVisibilityDecissionField()->getId();
            $data['visible_value'] = $field->getVisibilityValue();
            $this->get('visibility')->get('visible_value')->setAttribute('data-current_value', $field->getVisibilityValue());
        }

        $this->setData($data);
    }

    protected function _isTimeSlot()
    {
        return $this->_field->getType() == 'timeslot';
    }
}
