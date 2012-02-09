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
 
namespace CudiBundle\Form\Admin\Order;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	Zend\Form\Element\Submit;

class Edit extends \Admin\Form\Order\Add
{

    public function __construct($options = null)
    {
        parent::__construct($options);
		
        $this->removeElement('submit');

		$submit = new Submit('submit');
        $submit->setLabel('Edit')
                ->setAttrib('class', 'stock_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);
    }

	public function populate($order)
	{
		$data = array(
			'supplier' => $order->getSupplier()->getId()
		);
		
		parent::populate($data);
	}

}