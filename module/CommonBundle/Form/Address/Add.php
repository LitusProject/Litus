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
     * @var string
     */
    private $_prefix;

    /**
     * @param string $prefix
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($prefix = '', $name = null)
    {
        parent::__construct($name);

        $prefix = '' == $prefix ? '' : $prefix . '_';
        $this->_prefix = $prefix;

        $field = new Text($prefix . 'address_street');
        $field->setLabel('Street')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $this->add($field);

        $field = new Text($prefix . 'address_number');
        $field->setLabel('Number')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired();
        $this->add($field);

        $field = new Text($prefix . 'address_mailbox');
        $field->setLabel('Mailbox')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small');
        $this->add($field);

        $field = new Text($prefix . 'address_postal');
        $field->setLabel('Postal Code')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired();
        $this->add($field);

        $field = new Text($prefix . 'address_city');
        $field->setLabel('City')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setRequired();
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
                'required' => true,
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_number',
                'required' => true,
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
                'validators' => array(
                    array(
                        'name' => 'alnum',
                        'options' => array(
                            'allowWhiteSpace' => true,
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                array(
                                    'max' => 5,
                                )
                            )
                        )
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_postal',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'alnum',
                    ),
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_city',
                'required' => true,
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
                ),
            )
        );

        $inputs[] = $factory->createInput(
            array(
                'name'     => $this->_prefix . 'address_country',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            )
        );

        return $inputs;
    }
}
