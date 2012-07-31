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
 
namespace BrBundle\Form\Admin\Company;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Form\Admin\Address\Add as AddressForm,
    Doctrine\ORM\EntityManager,
    Zend\Form\Form,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text;

/**
 * Add a company.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param mixed $opts The validator's options
     */
    public function __construct($opts = null)
    {
        parent::__construct($opts);
        
        $field = new Text('company_name');
        $field->setLabel('Company Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('vat_number');
        $field->setLabel('VAT Number')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $this->addSubForm(new AddressForm(), 'address');
        
        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'companies_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
