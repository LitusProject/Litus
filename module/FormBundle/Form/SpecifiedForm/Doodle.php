<?php

namespace FormBundle\Form\SpecifiedForm;

use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use FormBundle\Component\Exception\UnsupportedTypeException;
use FormBundle\Entity\Field\TimeSlot as TimeSlotFieldEntity;
use FormBundle\Entity\Node\Entry as EntryEntity;
use FormBundle\Entity\Node\Form\Doodle as DoodleEntity;
use FormBundle\Entity\Node\GuestInfo as GuestInfoEntity;

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
    protected $person;

    /**
     * @var GuestInfoEntity
     */
    protected $guestInfo;

    /**
     * @var DoodleEntity
     */
    protected $form;

    /**
     * @var Language
     */
    protected $language;

    /**
     * @var EntryEntity
     */
    protected $entry;

    /**
     * @var boolean
     */
    protected $forceEdit;

    /**
     * @var array
     */
    protected $occupiedSlots;

    public function init()
    {
        parent::init();

        $editable = $this->form->canBeSavedBy($this->person) || $this->forceEdit;

        if ($this->person === null) {
            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'first_name',
                    'label'    => 'First Name',
                    'required' => true,
                    'value'    => $this->guestInfo ? $this->guestInfo->getFirstName() : '',
                    'options'  => array(
                        'input' => array(
                            'filter' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'last_name',
                    'label'    => 'Last Name',
                    'required' => true,
                    'value'    => $this->guestInfo ? $this->guestInfo->getLastName() : '',
                    'options'  => array(
                        'input' => array(
                            'filter' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'email',
                    'label'    => 'Email',
                    'required' => true,
                    'value'    => $this->guestInfo ? $this->guestInfo->getEmail() : '',
                    'options'  => array(
                        'input' => array(
                            'filter' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'EmailAddress'),
                            ),
                        ),
                    ),
                )
            );
        }

        $occupiedSlots = $this->getOccupiedSlots();

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($this->form);

        foreach ($fields as $fieldSpecification) {
            if ($fieldSpecification instanceof TimeSlotFieldEntity) {
                if (isset($occupiedSlots[$fieldSpecification->getId()])) {
                    $validators = array(
                        array(
                            'name'    => 'Identical',
                            'options' => array(
                                'token' => '0',
                            ),
                        ),
                    );
                } else {
                    $validators = array(
                        array(
                            'name'    => 'TimeSlot',
                            'options' => array(
                                'timeslot' => $fieldSpecification,
                                'person'   => $this->person,
                            ),
                        ),
                    );
                }

                $validators[] = array(
                    'name'    => 'MaxTimeSlots',
                    'options' => array(
                        'form' => $this->form,
                    ),
                );

                $field = array(
                    'type'       => 'checkbox',
                    'name'       => 'field-' . $fieldSpecification->getId(),
                    'class'      => 'checkbox',
                    'attributes' => array(
                        'id' => 'field-' . $fieldSpecification->getId(),
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
            $this->addSubmit($this->form->getSubmitText($this->language));
        }

        if ($this->entry !== null) {
            $this->bind($this->entry);
        }
    }

    /**
     * @param  Person|null $person
     * @return self
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @param  GuestInfoEntity|null $guestInfo
     * @return self
     */
    public function setGuestInfo(GuestInfoEntity $guestInfo = null)
    {
        $this->guestInfo = $guestInfo;

        return $this;
    }

    /**
     * @param  DoodleEntity $form
     * @return self
     */
    public function setForm(DoodleEntity $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @param  Language $language
     * @return self
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param  EntryEntity|null $entry
     * @return self
     */
    public function setEntry(EntryEntity $entry = null)
    {
        $this->entry = $entry;

        return $this;
    }

    /**
     * @param  boolean $forceEdit
     * @return self
     */
    public function setForceEdit($forceEdit)
    {
        $this->forceEdit = $forceEdit;

        return $this;
    }

    public function getOccupiedSlots()
    {
        if ($this->occupiedSlots !== null) {
            return $this->occupiedSlots;
        }

        $formEntries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($this->form);

        $this->occupiedSlots = array();
        foreach ($formEntries as $formEntry) {
            if (($this->person !== null && $formEntry->getCreationPerson() == $this->person)
                || ($this->guestInfo !== null && $formEntry->getGuestInfo() == $this->guestInfo)
            ) {
                continue;
            }

            foreach ($formEntry->getFieldEntries() as $fieldEntry) {
                $this->occupiedSlots[$fieldEntry->getField()->getId()] = $formEntry->getPersonInfo()->getFullName();
            }
        }

        return $this->occupiedSlots;
    }
}
