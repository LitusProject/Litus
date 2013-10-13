<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace FormBundle\Form\SpecifiedForm;

use CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Radio,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    FormBundle\Component\Exception\UnsupportedTypeException,
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
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\General\Language $language
     * @param \FormBundle\Entity\Node\Form $form
     * @param \CommonBundle\Entity\Users\Person|null $person
     * @param \FormBundle\Entity\Node\Entry|null $entry
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, Form $form, Person $person = null, Entry $entry = null, $name = null)
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
            if ($fieldSpecification instanceof TimeSlotField) {
                $field = new Checkbox('field-' . $fieldSpecification->getId());
            } else {
                throw new UnsupportedTypeException('This field type is unknown!');
            }

            $this->add($field);
        }

        $field = new Submit('submit');
        $field->setValue($form->getSubmitText($language))
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }
}