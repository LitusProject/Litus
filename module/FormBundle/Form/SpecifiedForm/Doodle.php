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

namespace FormBundle\Form\SpecifiedForm;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    FormBundle\Component\Exception\UnsupportedTypeException,
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

        if (null === $this->person) {
            $this->add(array(
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
            ));

            $this->add(array(
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
            ));

            $this->add(array(
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
                            array(
                                'name' => 'EmailAddress',
                            ),
                        ),
                    ),
                ),
            ));
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
                            'name' => 'Identical',
                            'options' => array(
                                'token' => '0',
                            ),
                        ),
                    );
                } else {
                    $validators = array(
                        array(
                            'name' => 'form_timeslot',
                            'options' => array(
                                'timeslot' => $fieldSpecification,
                                'person' => $this->person,
                            ),
                        ),
                    );
                }

                $validators[] = array(
                    'name' => 'form_max_timeslots',
                    'options' => array(
                        'form' => $this->form,
                    ),
                );

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
            $this->addSubmit($this->form->getSubmitText($this->language));
        }

        if (null !== $this->entry) {
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
        if (null !== $this->occupiedSlots) {
            return $this->occupiedSlots;
        }

        $formEntries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($this->form);

        $this->occupiedSlots = array();
        foreach ($formEntries as $formEntry) {
            if ((null !== $this->person && $formEntry->getCreationPerson() == $this->person) ||
                (null !== $this->guestInfo && $formEntry->getGuestInfo() == $this->guestInfo)) {
                continue;
            }

            foreach ($formEntry->getFieldEntries() as $fieldEntry) {
                $this->occupiedSlots[$fieldEntry->getField()->getId()] = $formEntry->getPersonInfo()->getFullName();
            }
        }

        return $this->occupiedSlots;
    }
}
