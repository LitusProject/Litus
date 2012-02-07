<?php

namespace CudiBundle\Form\Admin\Stock;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
		
	Zend\Validator\Int as IntValidator,
	
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text;
	
class Update extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setMethod('post');
		
        $field = new Text('number');
        $field->setLabel('Number')
        	->setRequired()
			->addValidator(new IntValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('updateStock');
        $field->setLabel('Update')
                ->setAttrib('class', 'stock_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

	public function populate($data)
	{
		parent::populate(array(
				'number' => $data->getNumberInStock()
			)
		);
	}
}