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

namespace CommonBundle\Form\Admin\Location;

use CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Form\Admin\Address\Add as AddressForm,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Location
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{

    /**
     * @var \CommonBundle\Form\Admin\Address\Add
     */
    private $_addressForm;

    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $this->_addressForm = new AddressForm('', 'address');
        $this->_addressForm->setLabel('Address');
        $this->add($this->_addressForm);

        $geographical = new Collection('greographical');
        $geographical->setLabel('Geographical');
        $this->add($geographical);

        $field = new Text('latitude');
        $field->setLabel('Latitude')
            ->setRequired();
        $geographical->add($field);

        $field = new Text('longitude');
        $field->setLabel('Longitude')
            ->setRequired();
        $geographical->add($field);

        $field = new Submit('add');
        $field->setValue('Add')
            ->setAttribute('class', 'location_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputs = $this->_addressForm->getInputs();
        foreach($inputs as $input)
            $inputFilter->add($input);

        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(),
                )
            )
        );

        return $inputFilter;
    }
}
