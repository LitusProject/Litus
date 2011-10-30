<?php

namespace Admin\Form\Sale;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;
use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Form\Form;
use \Zend\Form\Element\Hidden;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Form\Element\Textarea;
use \Zend\Validator;
use \Zend\Registry;

class SessionComment extends \Litus\Form\Admin\Form
{
    public function __construct($options = null )
    {
        parent::__construct($options);

        $field = new Textarea('comment');
        $field->setLabel('Comment');
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Edit Comment')
            ->setAttrib('class', 'sale_edit');
        $this->addElement($field);
    }

	public function populate($data)
	{
		parent::populate(array('comment' => $data->getComment()));
	}
}
