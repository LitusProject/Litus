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
 
namespace CudiBundle\Form\Admin\Sales\Session;

use CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Validator\Price as PriceValidator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Validator\Int as IntValidator;

/**
 * Add Sale Session content
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct(EntityManager $entityManager, $options = null )
    {
        parent::__construct($options);

        $units = $entityManager
            ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
            ->findAll();
        
        foreach($units as $unit) {
            $field = new Text('unit_' . $unit->getId());
            $field->setLabel('&euro; ' . number_format($unit->getUnit() / 100, 2))
                ->setAttrib('autocomplete', 'off')
                ->setRequired()
                ->setValue(0)
                ->addValidator(new IntValidator())
                ->setDecorators(array(new FieldDecorator()));
            $this->addElement($field);
        }
        
        $devices = $entityManager
            ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
            ->findAll();
        
        foreach($devices as $device) {
            $field = new Text('device_' . $device->getId());
            $field->setLabel($device->getName())
                ->setAttrib('autocomplete', 'off')
                ->setRequired()
                ->setValue(0)
                ->addValidator(new PriceValidator())
                ->setDecorators(array(new FieldDecorator()));
            $this->addElement($field);
        }

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'sale_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
    
    public function populateFromCashRegister(CashRegister $cashRegister)
    {
        $data = array();
        foreach($cashRegister->getBankDeviceAmounts() as $amount)
            $data['device_' . $amount->getDevice()->getId()] = $amount->getAmount() / 100;
        foreach($cashRegister->getMoneyUnitAmounts() as $amount)
            $data['unit_' . $amount->getUnit()->getId()] = $amount->getAmount();
        
        $this->populate($data);
    }
}
