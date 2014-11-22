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

use CommonBundle\Component\Validator\FieldLineLength as LengthValidator,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    FormBundle\Component\Exception\UnsupportedTypeException,
    FormBundle\Entity\Field\Checkbox as CheckboxFieldEntity,
    FormBundle\Entity\Field\Dropdown as DropdownFieldEntity,
    FormBundle\Entity\Field\File as FileFieldEntity,
    FormBundle\Entity\Field\String as StringFieldEntity,
    FormBundle\Entity\Node\Entry,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\GuestInfo;

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
    protected $_person;

    /**
    * @var GuestInfo
    */
    protected $_guestInfo;

    /**
     * @var Form
     */
    protected $_form;

    /**
    * @var Language
    */
    protected $_language;

    /**
    * @var Entry
    */
    protected $_entry;

    /**
     * @var boolean
     */
    protected $_isDraft;

    public function init()
    {
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

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($this->_form);

        foreach ($fields as $fieldSpecification) {
            $specification = array(
                'name'       => 'field-' . $fieldSpecification->getId(),
                'label'      => $fieldSpecification->getLabel($this->_language),
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
                    $specification['attributes']['maxlength'] = $fieldSpecification->getLineLength();
                    $specification['attributes']['data-linelen'] = $fieldSpecification->getLineLength();
                    $specification['attributes']['data-linecount'] = $fieldSpecification->getLines();

                    $specification['options']['input']['validators'] = array(
                        new LengthValidator(
                            $fieldSpecification->getLineLength(),
                            $fieldSpecification->getLines()
                        ),
                    );
                }
            } elseif ($fieldSpecification instanceof DropdownFieldEntity) {
                $specification['type'] = 'select';
                $specification['attributes']['options'] = $fieldSpecification->getOptionsArray($this->_language);
            } elseif ($fieldSpecification instanceof CheckboxFieldEntity) {
                $specification['type'] = 'checkbox';
            } elseif ($fieldSpecification instanceof FileFieldEntity) {
                $this->setAttribute('enctype', 'multipart/form-data');
                $specification['type'] = 'file';
                $specification['options']['input']['validators'] = array(
                    array(
                        'name' => 'filesize',
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

        if ($this->_form->isEditableByUser() && !$this->_isDraft) {
            $this->addSubmit('Save as Draft', 'btn-info', 'save_as_draft');
        }

        $this->addSubmit($this->_form->getSubmitText($this->_language));

        if (null !== $this->_entry) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->_entry));

            foreach ($this->_entry->getFieldEntries() as $fieldEntry) {
                if ($fieldEntry->getField() instanceof FileFieldEntity) {
                    $this->get('field-' . $fieldEntry->getField()->getId())
                        ->setAttribute('data-file', $fieldEntry->getValue())
                        ->setAttribute('data-name', $fieldEntry->getReadableValue());
                }
            }
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
    * @param  GuesetInfo $guestInfo
    * @return self
    */
    public function setGuestInfo(GuesetInfo $guestInfo = null)
    {
        $this->_guestInfo = $guestInfo;

        return $this;
    }

    /**
    * @param  Form $form
    * @return self
    */
    public function setForm(Form $form)
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
    * @param  Entry $entry
    * @return self
    */
    public function setEntry(Entry $entry = null)
    {
        $this->_entry = $entry;

        return $this;
    }

    /**
    * @param  boolean $isDraft
    * @return self
    */
    public function isDraft($isDraft)
    {
        $this->_isDraft = $isDraft;

        return $this;
    }
}
