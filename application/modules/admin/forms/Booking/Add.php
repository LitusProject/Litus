<?php

namespace Admin\Form\Booking;

use Zend\Form\SubForm;

use Litus\Entity\Cudi\Sales\BookingStatus;

use Litus\Validator\Price as PriceValidator;
use Litus\Validator\Year as YearValidator;

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
         
        // Create text fields that will contain information for the new booking
		$person = new Text('person');
        $person->setLabel('Person')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($person);

		$stockArticle = new Text('stockArticle');
        $stockArticle->setLabel('Article')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($stockArticle);

        // Create the button
        $submit = new Submit('submit');
        $submit->setLabel('Add')
                ->setAttrib('class', 'bookings_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);

    }
}