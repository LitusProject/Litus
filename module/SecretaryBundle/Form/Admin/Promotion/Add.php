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

namespace SecretaryBundle\Form\Admin\Promotion;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Text,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Promotion form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Checkbox('academic_add');
        $field->setLabel('Academic')
            ->setValue(true);
        $this->add($field);

        $academic = new Collection('academic');
        $academic->setLabel('Academic');
        $this->add($academic);

        $field = new Hidden('academic_id');
        $field->setAttribute('id', 'academicId');
        $academic->add($field);

        $field = new Text('academic_name');
        $field->setLabel('Academic')
            ->setAttribute('style', 'width: 500px')
            ->setAttribute('id', 'academicSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $academic->add($field);

        $external = new Collection('external');
        $external->setLabel('External');
        $this->add($external);

        $field = new Text('external_first_name');
        $field->setLabel('First Name')
            ->setRequired();
        $external->add($field);

        $field = new Text('external_last_name');
        $field->setLabel('Last Name')
            ->setRequired();
        $external->add($field);

        $field = new Text('external_email');
        $field->setLabel('Email')
            ->setRequired();
        $external->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $requireAcademic = isset($this->data['academic_add']) && $this->data['academic_add'];

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'academic_id',
                    'required' => $requireAcademic,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'academic_name',
                    'required' => $requireAcademic,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

         $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'external_first_name',
                    'required' => !$requireAcademic,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'external_last_name',
                    'required' => !$requireAcademic,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'external_email',
                    'required' => !$requireAcademic,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'EmailAddress',
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}