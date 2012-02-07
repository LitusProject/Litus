<?php

namespace CudiBundle\Form\Admin\Booking;

use Zend\Form\SubForm,

	CudiBundle\Entity\Sales\BookingStatus,
	
	CudiBundle\Component\Validator\ArticleBarcode as ArticleBarcodeValidator,
	CommonBundle\Component\Validator\Username as UsernameValidator,

	CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	
	Zend\Form\Form,
	Zend\Form\Element\Select,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	
	Zend\Validator\Int as IntValidator;

class Add extends \CommonBundle\Component\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAction('/admin/booking/add');
        $this->setMethod('post');
         
		$field = new Text('person');
        $field->setLabel('Person')
        	->setRequired()
			->addValidator(new UsernameValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$field = new Text('stockArticle');
        $field->setLabel('Article')
        	->setRequired()
			->addValidator(new ArticleBarcodeValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$field = new Text('number');
        $field->setLabel('Number')
        	->setRequired()
			->addValidator(new IntValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'bookings_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

    }
}