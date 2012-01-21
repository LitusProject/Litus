<?php

namespace Admin\Form\Stock;

use \Zend\Validator\Int as IntValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

use \Zend\Registry;

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