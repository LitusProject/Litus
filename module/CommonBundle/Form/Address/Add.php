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

namespace CommonBundle\Form\Address;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Validator\NotZero as NotZeroValidator,
    CommonBundle\Entity\General\Address,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Address
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Element\Collection
{
    /**
     * @var string The form's prefix
     */
    private $_prefix;

    /**
     * @var boolean Whether or not the form is required
     */
    private $_required;

    /**
     * @param string $prefix The form's prefix
     * @param null|string|int $name Optional name for the element
     * @param boolean $required Whether or not the address is required
     */
    public function __construct($prefix = '', $name = null, $required = true)
    {
        parent::__construct($name);

        $prefix = '' == $prefix ? '' : $prefix . '_';
        $this->_prefix = $prefix;
        $this->_required = $required;

        $field = new Text($prefix . 'address_street');
        $field->setLabel('Street')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Text($prefix . 'address_number');
        $field->setLabel('Number')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Text($prefix . 'address_mailbox');
        $field->setLabel('Mailbox')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small');
        $this->add($field);

        $field = new Text($prefix . 'address_postal');
        $field->setLabel('Postal Code')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Text($prefix . 'address_city');
        $field->setLabel('City')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setRequired($this->_required);
        $this->add($field);

        $field = new Select($prefix . 'address_country');
        $field->setLabel('Country')
            ->setAttribute('options', $this->_getCountries());
        $this->add($field);

        $this->populateValues(
            array(
                $prefix . 'address_country' => 'BE'
            )
        );
    }

    private function _getCountries()
    {
        $options = array();
        foreach(Address::$countries as $key => $continent) {
            $options[$key] = array(
                'label' => $key,
                'options' => $continent,
            );
        }
        return $options;
    }

    public function getInputs()
    {
        $factory = new InputFactory();
        $inputs = array();

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_street',
                'required' => $this->_required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_number',
                'required' => $this->_required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'alnum',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                    ),
                    new NotZeroValidator(),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_mailbox',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_postal',
                'required' => $this->_required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'digits',
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_city',
                'required' => $this->_required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_country',
                'required' => $this->_required,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            )
        );

        return $inputs;
    }
}
