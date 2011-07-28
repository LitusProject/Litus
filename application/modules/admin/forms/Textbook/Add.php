<?php

namespace Admin\Form\Textbook;

use Zend\Form\Element\Checkbox;

use Litus\Form\Decorator\FieldDecorator;

use Zend\Form\Decorator\Errors;

use Litus\Validator\PriceValidator;

use Zend\Validator\Regex;

use Zend\Validator\Int;

use Litus\Form\Decorator\DivSpanWrapper;

use Zend\Dojo\Form\Element\Button;

use \Zend\Form\Form;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

class Add extends \Litus\Form\Form
{
    public function __construct($parentOptions, $options = null)
    {
        parent::__construct($options);

        $this->setAttrib('id', 'add');

        // Create text fields that will contain information for the new course
        $title = new Text('title');
        $title->setLabel('Title')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($title);

        $author = new Text('author');
        $author->setLabel('Author')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($author);

        $publisher = new Text('publisher');
        $publisher->setLabel('Publisher')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($publisher);

        $year = new Text('year_published');
        $year->setLabel('Year published')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator('int');
        $this->addElement($year);

        $purchaseprice = new Text('purchaseprice');
        $purchaseprice->setLabel('Purchase price')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new PriceValidator());
        $this->addElement($purchaseprice);

        $sellprice = new Text('sellpricenomember');
        $sellprice->setLabel('Sell price')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new PriceValidator());
        $this->addElement($sellprice);

        $sellpricemember = new Text('sellpricemember');
        $sellpricemember->setLabel('Sell price (member)')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new PriceValidator());
        $this->addElement($sellpricemember);

        $supplier = new Text('supplier');
        $supplier->setLabel('Supplier')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($supplier);

        $bookable = new Checkbox('bookable');
        $bookable->setLabel('Bookable')
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($bookable);

        $unbookable = new Checkbox('unbookable');
        $unbookable->setLabel('Unbookable')
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($unbookable);

        // TODO: barcode: see zend barcode validator?
        // TODO: internal articles

        // Create the button

        $submit = new Submit('submit');
        $submit->setLabel('Add');
        $submit->setDecorators(array(
                                    'ViewHelper'
                               ));
        $this->addElement($submit);

    }
}