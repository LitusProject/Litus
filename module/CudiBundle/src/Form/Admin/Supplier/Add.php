<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Supplier;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Validator\PhoneNumber as PhoneNumberValidator,
    CommonBundle\Form\Admin\Address\Add as AddressForm,
    CudiBundle\Entity\Supplier,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text;

/**
 * Add Supplier
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('phone_number');
        $field->setLabel('Phone Number')
            ->setAttrib('placeholder', '+CCAAANNNNNN')
            ->addValidator(new PhoneNumberValidator())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $this->addSubForm(new AddressForm(), 'address');

        $field = new Text('vat_number');
        $field->setLabel('VAT Number')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'supplier_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    public function populateFromSupplier(Supplier $supplier)
    {
        $data = array(
            'name' => $supplier->getName(),
            'phone_number' => $supplier->getPhoneNumber(),
            'vat_number' => $supplier->getVatNumber(),
            'address_street' => $supplier->getAddress()->getStreet(),
            'address_number' => $supplier->getAddress()->getNumber(),
            'address_postal' => $supplier->getAddress()->getPostal(),
            'address_city' => $supplier->getAddress()->getCity(),
            'address_country' => $supplier->getAddress()->getCountryCode(),
        );

        $this->populate($data);
    }
}
