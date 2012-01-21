<?php

namespace SaleApp\Form\Queue;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;
use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Form\Form;
use \Zend\Form\Element\Hidden;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Validator;
use \Zend\Registry;

class SignIn extends \Litus\Form\Admin\Form
{
    public function __construct($options = null )
    {
        parent::__construct($options);

        $field = new Text('number');
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Sign in!');
        $this->addElement($field);

        parent::populate(array('number'=>""));
    }

    public function populate($value="") {
        parent::populate(array('number'=>$value));
    }

}
