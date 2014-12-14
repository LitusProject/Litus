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

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    FormBundle\Component\Exception\UnsupportedTypeException,
    FormBundle\Component\Validator\MaxTimeSlot as MaxTimeSlotValidator,
    FormBundle\Component\Validator\TimeSlot as TimeSlotValidator,
    FormBundle\Entity\Field\TimeSlot as TimeSlotFieldEntity,
    FormBundle\Entity\Node\Entry as EntryEntity,
    FormBundle\Entity\Node\Form\Doodle as DoodleEntity,
    FormBundle\Entity\Node\GuestInfo as GuestInfoEntity;

/**
 * Specifield Form Doodle
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doodle extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'FormBundle\Hydrator\Node\Entry\Doodle';

    /**
     * @var Person
     */
    protected $_person;

    /**
     * @var GuestInfoEntity
     */
    protected $_guestInfo;

    /**
     * @var DoodleEntity
     */
     protected $_form;

    /**
     * @var Language
     */
    protected $_language;

    /**
     * @var EntryEntity
     */
    protected $_entry;

    /**
     * @var boolean
     */
    protected $_forceEdit;

    /**
     * @var array
     */
    protected $_occupiedSlots;

    public function init()
    {
        parent::init();

        $editable = $this->_form->canBeSavedBy($this->_person) || $this->_forceEdit;

        if (null === $this->_person) {
            $this->add(array(
                'type'     => 'text',
                'name'     => 'first_name',
                'label'    => 'First Name',
                'required' => true,
                'value'    => $this->_guestInfo ? $this->_guestInfo->getFirstName() : '',
                'options'  => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ));

            $this->add(array(
                'type'     => 'text',
                'name'     => 'last_name',
                'label'    => 'Last Name',
                'required' => true,
                'value'    => $this->_guestInfo ? $this->_guestInfo->getLastName() : '',
                'options'  => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ));

            $this->add(array(
                'type'     => 'text',
                'name'     => 'email',
                'label'    => 'Email',
                'required' => true,
                'value'    => $this->_guestInfo ? $this->_guestInfo->getEmail() : '',
                'options'  => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'emailaddress',
                            ),
                        ),
                    ),
                ),
            ));
        }

        $occupiedSlots = $this->getOccupiedSlots();

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($this->_form);

        foreach ($fields as $fieldSpecification) {
            if ($fieldSpecification instanceof TimeSlotFieldEntity) {
                if (isset($occupiedSlots[$fieldSpecification->getId()])) {
                    $validators = array(
                        array(
                            'name' => 'Identical',
                            'options' => array(
                                'token' => '0',
                            ),
                        ),
                    );
                } else {
                    $validators = array(
                        new TimeSlotValidator($fieldSpecification, $this->getEntityManager(), $this->_person),
                    );
                }

                $validators[] = new MaxTimeSlotValidator($this->_form);

                $field = array(
                    'type'       => 'checkbox',
                    'name'       => 'field-' . $fieldSpecification->getId(),
                    'class'      => 'checkbox',
                    'attributes' => array(
                        'id'       => 'field-' . $fieldSpecification->getId(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'validators' => $validators,
                        ),
                    ),
                );

                if (!$editable || isset($occupiedSlots[$fieldSpecification->getId()])) {
                    $field['attributes']['disabled'] = 'disabled';
                }

                $this->add($field);
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }
        }

        if ($editable) {
            $this->addSubmit($this->_form->getSubmitText($this->_language));
        }

        if (null !== $this->_entry) {
            $this->bind($this->_entry);
        }
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson(Person $person = null)
    {
        $this->_person = $person;

        return $this;
    }

    /**
     * @param  GuestInfoEntity $guestInfo
     * @return self
     */
    public function setGuestInfo(GuestInfoEntity $guestInfo = null)
    {
        $this->_guestInfo = $guestInfo;

        return $this;
    }

    /**
     * @param  DoodleEntity $form
     * @return self
     */
    public function setForm(DoodleEntity $form)
    {
        $this->_form = $form;

        return $this;
    }

    /**
     * @param  Language $language
     * @return self
     */
    public function setLanguage(Language $language)
    {
        $this->_language = $language;

        return $this;
    }

    /**
     * @param  EntryEntity|null $entry
     * @return self
     */
    public function setEntry(EntryEntity $entry = null)
    {
        $this->_entry = $entry;

        return $this;
    }

    /**
    * @param  boolean $forceEdit
    * @return self
    */
    public function setForceEdit($forceEdit)
    {
        $this->_forceEdit = $forceEdit;

        return $this;
    }

    public function getOccupiedSlots()
    {
        if (null !== $this->_occupiedSlots) {
            return $this->_occupiedSlots;
        }

        $formEntries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($this->_form);

        $this->_occupiedSlots = array();
        foreach ($formEntries as $formEntry) {
            if ((null !== $this->_person && $formEntry->getCreationPerson() == $this->_person) ||
                (null !== $this->_guestInfo && $formEntry->getGuestInfo() == $this->_guestInfo)) {
                continue;
            }

            foreach ($formEntry->getFieldEntries() as $fieldEntry) {
                $this->_occupiedSlots[$fieldEntry->getField()->getId()] = $formEntry->getPersonInfo()->getFullName();
            }
        }

        return $this->_occupiedSlots;
    }
}
