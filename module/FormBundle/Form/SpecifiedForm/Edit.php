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
    FormBundle\Entity\Field\File as FileField,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Entry,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit,
    Zend\Validator\File\Size as SizeValidator;

/**
 * Specifield Form Edit
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager            $entityManager
     * @param \CommonBundle\Entity\General\Language  $language
     * @param \FormBundle\Entity\Node\Form           $form
     * @param \FormBundle\Entity\Node\Entry          $entry
     * @param \CommonBundle\Entity\Users\Person|null $person
     * @param null|string|int                        $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, Form $form, Entry $entry, Person $person = null, $name = null)
    {
        parent::__construct($entityManager, $language, $form, $person, $name);

        $field = new Submit('submit');
        $field->setValue($form->getUpdateText($language))
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);

        $this->_populateFromEntry($entry);
    }

    private function _populateFromEntry(Entry $entry)
    {
        $data = array();

        if ($entry->getGuestInfo()) {
            $data['first_name'] = $entry->getGuestInfo()->getFirstName();
            $data['last_name'] = $entry->getGuestInfo()->getLastName();
            $data['email'] = $entry->getGuestInfo()->getEmail();
        }

        foreach ($entry->getFieldEntries() as $fieldEntry) {
            $data['field-' . $fieldEntry->getField()->getId()] = $fieldEntry->getValue();
            if ($fieldEntry->getField() instanceof FileField) {
                $this->get('field-' .$fieldEntry->getField()->getId())
                    ->setAttribute('data-file', $fieldEntry->getValue());
            }
        }
        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        foreach ($this->_form->getFields() as $fieldSpecification) {
            if ($fieldSpecification instanceof FileField) {
                $inputFilter->remove('field-' . $fieldSpecification->getId());

                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'field-' . $fieldSpecification->getId(),
                            'required' => false,
                            'validators' => array(
                                new SizeValidator(array('max' => $fieldSpecification->getMaxSize() . 'MB'))
                            ),
                        )
                    )
                );
            }
        }

        return $inputFilter;
    }
}
