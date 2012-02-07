<?php

namespace CudiBundle\Form\Admin\Sale;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	
	Doctrine\ORM\EntityManager,
	
	CommonBundle\Component\Validator\Price as PriceValidator,
	
	Zend\Form\Element\Submit,
	Zend\Form\Element\Textarea;

class SessionComment extends \CommonBundle\Component\Form\Admin\Form
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
