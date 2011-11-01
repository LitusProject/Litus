<?php

namespace Admin\Form\Booking;

use Zend\Form\SubForm;

use Litus\Entity\Cudi\Sales\BookingStatus;

use Litus\Validator\ArticleBarcode as ArticleBarcodeValidator;
use Litus\Validator\Username as UsernameValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use Litus\Form\Admin\Decorator\FieldDecorator;

use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;

class Add extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAction('/admin/booking/add');
        $this->setMethod('post');
         
		$person = new Text('person');
        $person->setLabel('Person')
        	->setRequired()
			->addValidator(new UsernameValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($person);

		$stockArticle = new Text('stockArticle');
        $stockArticle->setLabel('Article')
        	->setRequired()
			->addValidator(new ArticleBarcodeValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($stockArticle);

        $submit = new Submit('submit');
        $submit->setLabel('Add')
                ->setAttrib('class', 'bookings_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);

    }
}