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
    FormBundle\Entity\Field\Checkbox as CheckboxFieldEntity,
    FormBundle\Entity\Field\Dropdown as DropdownFieldEntity,
    FormBundle\Entity\Field\File as FileFieldEntity,
    FormBundle\Entity\Field\String as StringFieldEntity,
    FormBundle\Entity\Node\Entry as EntryEntity,
    FormBundle\Entity\Node\Form\Form as FormEntity,
    FormBundle\Entity\Node\GuestInfo as GuestInfoEntity;

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
     * @var boolean
     */
    protected $isDraft;

    public function init()
    {
        if (!($this->form instanceof FormEntity)) {
            return;
        }

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
                            'name'    => 'field_line_length',
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
            } elseif ($fieldSpecification instanceof FileFieldEntity) {
                $specification['type'] = 'file';
                $specification['options']['input']['validators'] = array(
                    array(
                        'name'    => 'filesize',
                        'options' => array(
                            'max' => $fieldSpecification->getMaxSize() . 'MB',
                        ),
                    ),
                );
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }

            if (null !== $fieldSpecification->getVisibilityDecissionField()) {
                $specification['attributes']['data-visible_if_element'] = $fieldSpecification->getVisibilityDecissionField()->getId();
                $specification['attributes']['data-visible_if_value'] = $fieldSpecification->getVisibilityValue();
            }
            $this->add($specification);
        }

        if ($this->form->isEditableByUser() && !$this->isDraft) {
            $this->addSubmit('Save as Draft', 'btn-info', 'save_as_draft');
        }

        $this->addSubmit($this->form->getSubmitText($this->language));

        if (null !== $this->entry) {
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
     * @param  boolean $isDraft
     * @return self
     */
    public function isDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }
}
