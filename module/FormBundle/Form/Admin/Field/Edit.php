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

use Doctrine\ORM\EntityManager,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\String as StringField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
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
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(Field $fieldSpecification, EntityManager $entityManager, $name = null)
    {
        parent::__construct($fieldSpecification->getForm(), $entityManager, $name);

        $this->_field = $fieldSpecification;

        $this->get('type')->setAttribute('disabled', 'disabled');
        $this->get('visibility')->get('visible_if')->setAttribute('options', $this->_getVisibilityOptions());

        $this->remove('submit');
        $this->remove('submit_repeat');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'field_edit');
        $this->add($field);

        $this->populateFromField($fieldSpecification);
    }

    private function _getVisibilityOptions()
    {
        $options = array(0 => 'Always');
        foreach ($this->_form->getFields() as $field) {
            if ($field->getId() == $this->_field->getId())
                continue;

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

    protected function _isTimeSlot()
    {
        return $this->_field->getType() == 'timeslot';
    }
}
