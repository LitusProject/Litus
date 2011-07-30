<?php

namespace Admin\Form\Textbook;

use Zend\Form\SubForm;

use Litus\Validator\PriceValidator;

use Litus\Form\Decorator\FieldDecorator;

use Zend\Form\Form;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Checkbox;


class Add extends \Litus\Form\Form
{

    /**
     *
     * This variable contains the subform for internal articles.
     * @var SubForm
     */
    private $internal_form;

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAttrib('id', 'add')
        ->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'form'));
         
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
         
        // TODO: supplier when db is ready for it
        //     	$supplier = new Text('supplier');
        //     	$supplier->setLabel('Supplier')
        //     		->setRequired()
        //     		->setDecorators(array(new FieldDecorator()));
        //     	$form->addElement($supplier);
         
        $bookable = new Checkbox('bookable');
        $bookable->setLabel('Bookable')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($bookable);
         
        $unbookable = new Checkbox('unbookable');
        $unbookable->setLabel('Unbookable')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($unbookable);
         
        $internal = new Checkbox('internal');
        $internal->setLabel('Internal article')
        ->setDecorators(array(new FieldDecorator()));
        $internal->setAttrib('onclick', "toggle_visibility()");
        $this->addElement($internal);
         
        // TODO: barcode: see zend barcode validator?
        // TODO: internal articles

        // Create the subform for internal articles
        $this->internal_form = new AddInternal();
        $this->addSubForm($this->internal_form, 'internalform');

        // Create the button
        $submit = new Submit('submit');
        $submit->setLabel('Add');
        $submit->setDecorators(array(
    		'ViewHelper'
        ));
        $this->addElement($submit);

    }

    /**
     *
     * This method returns the subform for internal articles.
     * @return SubForm
     */
    public function getInternalForm() {
        return $this->internal_form;
    }
}