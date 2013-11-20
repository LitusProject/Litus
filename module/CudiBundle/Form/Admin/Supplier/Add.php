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

namespace CudiBundle\Form\Admin\Supplier;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Validator\PhoneNumber as PhoneNumberValidator,
    CommonBundle\Form\Admin\Address\Add as AddressForm,
    CudiBundle\Entity\Supplier,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Supplier
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

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('phone_number');
        $field->setLabel('Phone Number')
            ->setAttribute('placeholder', '+CCAAANNNNNN');
        $this->add($field);

        $field = new Checkbox('contact');
        $field->setLabel('Contact');
        $this->add($field);

        $field = new AddressForm('address', 'address');
        $field->setLabel('Address');
        $this->add($field);

        $field = new Text('vat_number');
        $field->setLabel('VAT Number');
        $this->add($field);

        $field = new Select('template');
        $field->setLabel('Template')
            ->setAttribute('options', Supplier::$POSSIBLE_TEMPLATES)
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'supplier_add');
        $this->add($field);
    }

    public function populateFromSupplier(Supplier $supplier)
    {
        $data = array(
            'name' => $supplier->getName(),
            'phone_number' => $supplier->getPhoneNumber(),
            'vat_number' => $supplier->getVatNumber(),
            'contact' => $supplier->isContact(),
            'template' => $supplier->getTemplate(),
            'address_address_street' => $supplier->getAddress()->getStreet(),
            'address_address_number' => $supplier->getAddress()->getNumber(),
            'address_address_mailbox' => $supplier->getAddress()->getMailbox(),
            'address_address_postal' => $supplier->getAddress()->getPostal(),
            'address_address_city' => $supplier->getAddress()->getCity(),
            'address_address_country' => $supplier->getAddress()->getCountryCode(),
        );

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputs = $this->get('address')
            ->getInputs();
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
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'phone_number',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PhoneNumberValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'template',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
