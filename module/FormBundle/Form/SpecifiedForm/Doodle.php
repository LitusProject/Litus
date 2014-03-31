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
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    FormBundle\Component\Exception\UnsupportedTypeException,
    FormBundle\Component\Validator\TimeSlot as TimeSlotValidator,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Entry,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Specifield Form Doodle
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doodle extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \FormBundle\Entity\Node\Form
     */
    protected $_form;

    /**
     * @var array
     */
    private $_occupiedSlots;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Entity\User\Person
     */
    private $_person;

    /**
     * @param \Doctrine\ORM\EntityManager           $entityManager
     * @param \CommonBundle\Entity\General\Language $language
     * @param \FormBundle\Entity\Node\Form          $form
     * @param null|Person                           $person
     * @param \FormBundle\Entity\Node\Entry|null    $entry
     * @param boolean                               $forceEdit
     * @param null|string|int                       $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, Form $form, Person $person = null, Entry $entry = null, $forceEdit = false, $name = null)
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

        $this->_entityManager = $entityManager;
        $this->_form = $form;
        $this->_person = $person;

        $this->_occupiedSlots = $this->_getOccupiedSlots($entityManager, $form, $person);

        $editable = $form->canBeSavedBy($person) || $forceEdit;

        // Fetch the fields through the repository to have the correct order
        $fields = $entityManager
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($form);

        foreach ($fields as $fieldSpecification) {
            if ($fieldSpecification instanceof TimeSlotField) {
                $field = new Checkbox('field-' . $fieldSpecification->getId());
                $field->setAttribute('class', 'checkbox');

                if (!$editable)
                    $field->setAttribute('disabled', 'disabled');
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }

            $this->add($field);
        }

        if ($editable) {
            $field = new Submit('submit');
            $field->setValue($form->getSubmitText($language))
                ->setAttribute('class', 'btn btn-primary');
            $this->add($field);
        }

        if (null !== $entry)
            $this->_populateFromEntry($entry);

        $this->_disableOccupiedSlots($this->_occupiedSlots);
    }

    private function _getOccupiedSlots(EntityManager $entityManager, Form $form, Person $person = null)
    {
        $formEntries = $entityManager
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($form);

        $occupiedSlots = array();
        foreach ($formEntries as $formEntry) {
            if (null !== $person && $formEntry->getCreationPerson() == $person)
                continue;

            foreach ($formEntry->getFieldEntries() as $fieldEntry) {
                $occupiedSlots[$fieldEntry->getField()->getId()] = $formEntry->getPersonInfo()->getFullName();
            }
        }

        return $occupiedSlots;
    }

    public function getOccupiedSlots()
    {
        return $this->_occupiedSlots;
    }

    private function _populateFromEntry(Entry $entry)
    {
        $formData = array();

        if ($entry->isGuestEntry()) {
            $formData['first_name'] = $entry->getGuestInfo()->getFirstName();
            $formData['last_name'] = $entry->getGuestInfo()->getLastName();
            $formData['email'] = $entry->getGuestInfo()->getEmail();
        }

        foreach ($entry->getFieldEntries() as $fieldEntry) {
            $formData['field-' . $fieldEntry->getField()->getId()] = true;
        }

        $this->setData($formData);
    }

    private function _disableOccupiedSlots(array $occupiedSlots)
    {
        foreach ($occupiedSlots as $id => $slot) {
            $this->get('field-' . $id)->setAttribute('disabled', 'disabled');
        }
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach ($this->_form->getFields() as $fieldSpecification) {
            if ($fieldSpecification instanceof TimeSlotField) {
                if (isset($this->_occupiedSlots[$fieldSpecification->getId()])) {
                    $inputFilter->add(
                        $factory->createInput(
                            array(
                                'name'     => 'field-' . $fieldSpecification->getId(),
                                'required' => false,
                                'validators' => array(
                                    array(
                                        'name' => 'Identical',
                                        'options' => array(
                                            'token' => '0',
                                        ),
                                    ),
                                )
                            )
                        )
                    );
                } else {
                    $inputFilter->add(
                        $factory->createInput(
                            array(
                                'name'     => 'field-' . $fieldSpecification->getId(),
                                'required' => false,
                                'validators' => array(
                                    new TimeSlotValidator($fieldSpecification, $this->_entityManager, $this->_person),
                                ),
                            )
                        )
                    );
                }
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }
        }

        return $inputFilter;
    }
}
