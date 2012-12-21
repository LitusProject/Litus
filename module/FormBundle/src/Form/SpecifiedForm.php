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

namespace FormBundle\Form;

use CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    FormBundle\Component\Exception\UnsupportedTypeException,
    FormBundle\Entity\Fields\Checkbox as CheckboxField,
    FormBundle\Entity\Fields\String as StringField,
    FormBundle\Entity\Fields\Dropdown,
    FormBundle\Entity\Nodes\Form,
    FormBundle\Entity\Nodes\Entry,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Field
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class SpecifiedForm extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \CudiBundle\Entity\Sales\Article
     */
    protected $_form;

    /**
     * @param \CudiBundle\Entity\Sales\Form $form
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, Form $form, Person $person = null, $name = null)
    {
        parent::__construct($name);

        // Create guest fields
        if ($person === null) {
            $field = new Text('first_name');
            $field->setLabel('First Name')
                ->setRequired(true);
            $this->add($field);

            $field = new Text('last_name');
            $field->setLabel('Last Name')
                ->setRequired(true);
            $this->add($field);

            $field = new Text('email');
            $field->setLabel('Email Address')
                ->setRequired(true);
            $this->add($field);
        }

        $this->_form = $form;

        // Fetch the fields through the repository to have the correct order
        $fields = $entityManager
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($form);

        foreach ($fields as $fieldSpecification) {
            if ($fieldSpecification instanceof StringField) {
                $field = new Text('field-' . $fieldSpecification->getId());
                $field->setLabel($fieldSpecification->getLabel($language))
                    ->setRequired($fieldSpecification->isRequired());
                $this->add($field);
            } elseif ($fieldSpecification instanceof Dropdown) {
                $field = new Select('field-' . $fieldSpecification->getId());
                $field->setLabel($fieldSpecification->getLabel($language))
                    ->setAttribute('options', $fieldSpecification->getOptionsArray($language));
                $this->add($field);
            } elseif ($fieldSpecification instanceof CheckboxField) {
                $field = new Checkbox('field-' . $fieldSpecification->getId());
                $field->setLabel($fieldSpecification->getLabel($language));
                $this->add($field);
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }
        }

        $field = new Submit('submit');
        $field->setValue($form->getSubmitText($language))
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

    public function populateFromEntry(Entry $entry) {
        $formData = array();
        foreach ($entry->getFieldEntries() as $fieldEntry) {
            $formData['field-' . $fieldEntry->getField()->getId()] = $fieldEntry->getValue();
        }

        $this->setData($formData);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach ($this->_form->getFields() as $fieldSpecification) {
            if ($fieldSpecification instanceof StringField) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'field-' . $fieldSpecification->getId(),
                            'required' => $fieldSpecification->isRequired(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );
            } elseif ($fieldSpecification instanceof Dropdown) {
            } elseif ($fieldSpecification instanceof CheckboxField) {
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }
        }

        return $inputFilter;
    }
}
