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
 
namespace CudiBundle\Form\Admin\Sale;

use CommonBundle\Entity\General\Bank\CashRegister,
	CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit;

/**
 * Close Cash Register
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CashRegisterClose extends CashRegisterAdd
{
    public function __construct(EntityManager $entityManager, CashRegister $cashRegister, $options = null )
    {
        parent::__construct($entityManager, $options);

		$this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Close')
            ->setAttrib('class', 'sale_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $data = array();
        foreach($cashRegister->getBankDeviceAmounts() as $amount)
        	$data['device_' . $amount->getDevice()->getId()] = $amount->getAmount() / 100;
        foreach($cashRegister->getMoneyUnitAmounts() as $amount)
        	$data['unit_' . $amount->getUnit()->getId()] = $amount->getAmount();
       	$this->populate($data);
    }
}
