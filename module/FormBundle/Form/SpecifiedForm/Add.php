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

namespace FormBundle\Form\SpecifiedForm;

use CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Textarea,
    CommonBundle\Component\Form\Bootstrap\Element\File,
    CommonBundle\Component\Validator\FieldLineLength as LengthValidator,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    FormBundle\Component\Exception\UnsupportedTypeException,
    FormBundle\Entity\Field\Checkbox as CheckboxField,
    FormBundle\Entity\Field\String as StringField,
    FormBundle\Entity\Field\Dropdown as DropdownField,
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Entry,
    FormBundle\Entity\Node\GuestInfo,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Specifield Form Add
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \FormBundle\Entity\Node\Form
     */
    protected $_form;

    /**
     * @param \Doctrine\ORM\EntityManager           $entityManager
     * @param \CommonBundle\Entity\General\Language $language
     * @param \FormBundle\Entity\Node\Form          $form
     * @param null|Person                           $person
     * @param null|string|int                       $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, Form $form, Person $person = null, $name = null)
    {
        parent::__construct($name);

        // Create guest fields
        if (null === $person) {
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

                if ($fieldSpecification->isMultiLine()) {
                    $field = new Textarea('field-' . $fieldSpecification->getId());
                    $field->setAttribute('rows', 3);
                } else {
                    $field = new Text('field-' . $fieldSpecification->getId());
                }

                $field->setLabel($fieldSpecification->getLabel($language))
                    ->setRequired($fieldSpecification->isRequired());

                if ($fieldSpecification->hasLengthSpecification()) {
                    $field->setAttribute('class', $field->getAttribute('class') . ' count')
                        ->setAttribute('maxlength', $fieldSpecification->getLineLength())
                        ->setAttribute('data-linelen', $fieldSpecification->getLineLength())
                        ->setAttribute('data-linecount', $fieldSpecification->getLines());
                }

            } elseif ($fieldSpecification instanceof DropdownField) {
                $field = new Select('field-' . $fieldSpecification->getId());
                $field->setLabel($fieldSpecification->getLabel($language))
                    ->setAttribute('options', $fieldSpecification->getOptionsArray($language));
            } elseif ($fieldSpecification instanceof CheckboxField) {
                $field = new Checkbox('field-' . $fieldSpecification->getId());
                $field->setLabel($fieldSpecification->getLabel($language));
            } elseif ($fieldSpecification instanceof FileField) {
                $field = new File('field-' . $fieldSpecification->getId());
                $field->setLabel($fieldSpecification->getLabel($language));
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }

            if (null !== $fieldSpecification->getVisibilityDecissionField()) {
                $field->setAttribute('data-visible_if_element', $fieldSpecification->getVisibilityDecissionField()->getId())
                    ->setAttribute('data-visible_if_value', $fieldSpecification->getVisibilityValue());
            }
            $this->add($field);
        }

        $field = new Submit('save_as_draft');
        $field->setValue('Save as Draft')
            ->setAttribute('class', 'btn btn-info');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue($form->getSubmitText($language))
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

    public function populateFromEntry(Entry $entry)
    {
        $formData = array();

        if ($entry->isGuestEntry()) {
            $formData['first_name'] = $entry->getGuestInfo()->getFirstName();
            $formData['last_name'] = $entry->getGuestInfo()->getLastName();
            $formData['email'] = $entry->getGuestInfo()->getEmail();
        }

        foreach ($entry->getFieldEntries() as $fieldEntry) {
            $formData['field-' . $fieldEntry->getField()->getId()] = $fieldEntry->getValue();
        }

        $this->setData($formData);
    }

    /**
     * @param boolean $hasDraft
     */
    public function hasDraft($hasDraft)
    {
        if ($hasDraft) {
            $this->get('save_as_draft')->setAttribute('disabled', 'disabled');
        } else {
            $this->get('save_as_draft')->setAttribute('disabled', null);
        }
    }

    public function populateFromGuestInfo(GuestInfo $guestInfo)
    {
        $data = array(
            'first_name' => $guestInfo->getFirstName(),
            'last_name' => $guestInfo->getLastName(),
            'email' => $guestInfo->getEmail(),
        );

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach ($this->_form->getFields() as $fieldSpecification) {
            if ($fieldSpecification instanceof StringField) {

                $validators = array();
                if ($fieldSpecification->hasLengthSpecification()) {
                    $validators[] = new LengthValidator(
                        $fieldSpecification->getLineLength(),
                        $fieldSpecification->getLines()
                    );
                }

                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'field-' . $fieldSpecification->getId(),
                            'required' => $fieldSpecification->isRequired(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => $validators,
                        )
                    )
                );
            } elseif ($fieldSpecification instanceof DropdownField) {
            } elseif ($fieldSpecification instanceof CheckboxField) {
            } elseif ($fieldSpecification instanceof FileField) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'field-' . $fieldSpecification->getId(),
                            'required' => false,
                            'validators' => array(
                                array(
                                    'name' => 'filefilessize',
                                    'options' => array(
                                        'max' => $fieldSpecification->getMaxSize() . 'MB',
                                    ),
                                ),
                            ),
                        )
                    )
                );
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }
        }

        return $inputFilter;
    }
}
