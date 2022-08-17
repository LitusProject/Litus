<?php

namespace FormBundle\Form\SpecifiedForm;

use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use FormBundle\Component\Exception\UnsupportedTypeException;
use FormBundle\Entity\Field\Checkbox as CheckboxFieldEntity;
use FormBundle\Entity\Field\Dropdown as DropdownFieldEntity;
use FormBundle\Entity\Field\File as FileFieldEntity;
use FormBundle\Entity\Field\Text as StringFieldEntity;
use FormBundle\Entity\Node\Entry as EntryEntity;
use FormBundle\Entity\Node\Form\Form as FormEntity;
use FormBundle\Entity\Node\GuestInfo as GuestInfoEntity;
use TicketBundle\Entity\Event as Event;
use Zend\Validator\Identical;

/**
 * Specifield Form Add
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'FormBundle\Hydrator\Node\Entry';

    /**
     * @var Person
     */
    protected $person;

    /**
     * @var GuestInfoEntity
     */
    protected $guestInfo;

    /**
     * @var FormEntity
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
     * @var Event
     */
    protected $event;

    /**
     * @var boolean
     */
    protected $isEventForm;

    /**
     * @var boolean
     */
    protected $isDraft;

    public function init()
    {
        if (!($this->form instanceof FormEntity)) {
            return;
        }

        if ($this->person === null) {
            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'first_name',
                    'label'    => 'First Name',
                    'required' => false,
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
                    'required' => false,
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
                    'required' => false,
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

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'organization',
                    'label'    => 'Organization',
                    'required' => false,
                    'value'    => $this->guestInfo ? $this->guestInfo->getOrganization() : '',
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
                    'name'     => 'identification',
                    'label'    => 'R-number',
                    'required' => false,
                    'value'    => $this->guestInfo ? $this->guestInfo->getUniversityIdentification() : '',
                    'options'  => array(
                        'input' => array(
                            'filter' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );
        }

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($this->form);

        foreach ($fields as $fieldSpecification) {
            $specification = array(
                'name'       => 'field-' . $fieldSpecification->getId(),
                'label'      => $fieldSpecification->getLabel($this->language),
                'required'   => $fieldSpecification->isRequired(),
                'attributes' => array(
                    'id' => 'field-' . $fieldSpecification->getId(),
                ),
            );
            if ($fieldSpecification instanceof StringFieldEntity) {
                if ($fieldSpecification->isMultiLine()) {
                    $specification['type'] = 'textarea';
                    $specification['attributes']['rows'] = 3;
                } else {
                    $specification['type'] = 'text';
                }

                $specification['options']['input']['filters'] = array(
                    array('name' => 'StringTrim'),
                );

                if ($fieldSpecification->hasLengthSpecification()) {
                    $specification['attributes']['class'] = 'count';
                    $specification['attributes']['maxlength'] = $fieldSpecification->getLineLength() * $fieldSpecification->getLines();
                    $specification['attributes']['data-linelen'] = $fieldSpecification->getLineLength();
                    $specification['attributes']['data-linecount'] = $fieldSpecification->getLines();

                    $specification['options']['input']['validators'] = array(
                        array(
                            'name'    => 'FieldLineLength',
                            'options' => array(
                                'chars_per_line' => $fieldSpecification->getLineLength(),
                                'lines'          => $fieldSpecification->getLines(),
                            ),
                        ),
                    );
                }
            } elseif ($fieldSpecification instanceof DropdownFieldEntity) {
                $specification['type'] = 'select';
                $specification['attributes']['options'] = $fieldSpecification->getOptionsArray($this->language);
            } elseif ($fieldSpecification instanceof CheckboxFieldEntity) {
                $specification['type'] = 'checkbox';
                if ($fieldSpecification->isRequired()) {
                    $specification['options']['input']['validators'] = array(
                        array(
                            'name' => 'RequiredCheckbox',
                        ),
                    );
                }
            } elseif ($fieldSpecification instanceof FileFieldEntity) {
                $specification['type'] = 'file';
                $specification['options']['input']['validators'] = array(
                    array(
                        'name'    => 'FileSize',
                        'options' => array(
                            'max' => $fieldSpecification->getMaxSize() . 'MB',
                        ),
                    ),
                );
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }

            if ($fieldSpecification->getVisibilityDecissionField() !== null) {
                $specification['attributes']['data-visible_if_element'] = $fieldSpecification->getVisibilityDecissionField()->getId();
                $specification['attributes']['data-visible_if_value'] = $fieldSpecification->getVisibilityValue();
            }
            $this->add($specification);
        }

        if ($this->isEventForm) {
            $this->add(
                array(
                    'type'     => 'fieldset',
                    'name'     => 'spacer',
                    'label'    => 'Tickets',
                    'elements' => array(
                        // intentionally empty
                    ),
                )
            );

            if ($this->event->getOptions()->isEmpty()) {
                $this->add(
                    array(
                        'type'       => 'select',
                        'name'       => 'number_member',
                        'label'      => 'Number Member',
                        'attributes' => array(
                            'options' => $this->getNumberOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required' => true,
                            ),
                        ),
                    )
                );

                if (!$this->event->isOnlyMembers()) {
                    $this->add(
                        array(
                            'type'       => 'select',
                            'name'       => 'number_non_member',
                            'label'      => 'Number Non Member',
                            'attributes' => array(
                                'options' => $this->getNumberOptions(),
                            ),
                            'options'    => array(
                                'input' => array(
                                    'required' => true,
                                ),
                            ),
                        )
                    );
                }
            } else {
                foreach ($this->event->getOptions() as $option) {
                    $this->add(
                        array(
                            'type'       => 'select',
                            'name'       => 'option_' . $option->getId() . '_number_member',
                            'label'      => $option->getPriceNonMembers() != 0 ? ucfirst($option->getName()) . ' (Member)' : ucfirst($option->getName()),
                            'attributes' => array(
                                'options' => $this->getNumberOptions(),
                            ),
                            'options'    => array(
                                'input' => array(
                                    'required' => true,
                                ),
                            ),
                        )
                    );

                    if (!$this->event->isOnlyMembers() && $option->getPriceNonMembers() != 0) {
                        $this->add(
                            array(
                                'type'       => 'select',
                                'name'       => 'option_' . $option->getId() . '_number_non_member',
                                'label'      => ucfirst($option->getName()) . ' (Non Member)',
                                'attributes' => array(
                                    'options' => $this->getNumberOptions(),
                                ),
                                'options'    => array(
                                    'input' => array(
                                        'required' => true,
                                    ),
                                ),
                            )
                        );
                    }
                }
            }

            $this->add(
                array(
                    'type'       => 'checkbox',
                    'name'       => 'images',
                    'label'      => 'Hierbij geef ik toestemming om beeldmateriaal van mij te maken en te gebruiken (indien een gepubliceerde foto niet gewenst is kan je een mail sturen naar communicatie@vtk.be)',
                    'attributes' => array(
                        'id' => 'images',
                    ),
                    'options'    => array(
                        'input' => array(
                            'validators' => array(
                                array(
                                    'name'    => 'identical',
                                    'options' => array(
                                        'token'    => true,
                                        'strict'   => false,
                                        'messages' => array(
                                            Identical::NOT_SAME => 'You must agree to the terms and conditions.',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            );

            $this->add(
                array(
                    'type'       => 'checkbox',
                    'name'       => 'conditions',
                    //                'label'      => 'I have read and accept the GDPR terms and condition specified above',
                    'label'      => 'Bij deze ga ik akkoord dat VTK mijn gegevens mag gebruiken voor de werking van deze activiteit, om te gebruiken voor noodgevallen en mij te contacteren. Na afloop van de contacttracing worden mijn gegevens verwijderd.',
                    'attributes' => array(
                        'id' => 'conditions',
                    ),
                    'options'    => array(
                        'input' => array(
                            'validators' => array(
                                array(
                                    'name'    => 'identical',
                                    'options' => array(
                                        'token'    => true,
                                        'strict'   => false,
                                        'messages' => array(
                                            Identical::NOT_SAME => 'You must agree to the terms and conditions.',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            );
        }

        if ($this->form->isEditableByUser() && !$this->isDraft) {
            $this->addSubmit('Save as Draft', 'btn-info', 'save_as_draft');
        }

        $this->addSubmit($this->form->getSubmitText($this->language));

        if ($this->entry !== null) {
            $this->bind($this->entry);

            foreach ($this->entry->getFieldEntries() as $fieldEntry) {
                if ($fieldEntry->getField() instanceof FileFieldEntity) {
                    $this->get('field-' . $fieldEntry->getField()->getId())
                        ->setAttribute('data-file', $fieldEntry->getValue())
                        ->setAttribute('data-name', $fieldEntry->getReadableValue());
                }
            }
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
     * @param  FormEntity $form
     * @return self
     */
    public function setForm(FormEntity $form)
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
     * @param Event|null $event
     * @return self
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param bool $isEventForm
     * @return self
     */
    public function setIsEventForm(bool $isEventForm = false)
    {
        $this->isEventForm = $isEventForm;

        return $this;
    }

    /**
     * @param  boolean $isDraft
     * @return self
     */
    public function isDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    private function getNumberOptions()
    {
        $numbers = array();
        $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();

        for ($i = 0; $i <= $max; $i++) {
            $numbers[$i] = $i;
        }

        return $numbers;
    }
}
