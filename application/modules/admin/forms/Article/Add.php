<?php

namespace Admin\Form\Article;

use \Litus\Validator\Price as PriceValidator;
use \Litus\Validator\Year as YearValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Checkbox;
use \Zend\Form\SubForm;

use \Zend\Registry;

class Add extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setMethod('post');
         
        $field = new Text('title');
        $field->setLabel('Title')
			->setAttrib('size', 70)
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Text('author');
        $field->setLabel('Author')
			->setAttrib('size', 60)
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Text('publisher');
        $field->setLabel('Publisher')
			->setAttrib('size', 40)
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Text('year_published');
        $field->setLabel('Year Published')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
			->addValidator('int')
        	->addValidator(new YearValidator());
        $this->addElement($field);

		$field = new Checkbox('stock');
        $field->setLabel('Stock Article')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$this->addDisplayGroup(
			array(
				'title',
		        'author',
		        'publisher',
				'year_published',
				'stock'
		    ),
		    'article_form'
		);
		$this->getDisplayGroup('article_form')
		   	->setLegend('Article')
		    ->setAttrib('id', 'article_form')
		    ->removeDecorator('DtDdWrapper');
         
        $field = new Text('purchase_price');
        $field->setLabel('Purchase Price')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new PriceValidator());
        $this->addElement($field);
         
        $field = new Text('sellprice_nomember');
        $field->setLabel('Sell Price')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new PriceValidator());
        $this->addElement($field);
         
        $field = new Text('sellprice_member');
        $field->setLabel('Sell Price (Member)')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new PriceValidator());
        $this->addElement($field);
		
		$field = new Text('barcode');
        $field->setLabel('Barcode')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
		
        $field = new Select('supplier');
        $field->setLabel('Supplier')
        	->setRequired()
			->setMultiOptions($this->_getSuppliers())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Checkbox('bookable');
        $field->setLabel('Bookable')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Checkbox('unbookable');
        $field->setLabel('Unbookable')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$field = new Checkbox('can_expire');
        $field->setLabel('Can Expire')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Checkbox('internal');
        $field->setLabel('Internal Article')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
		
		$this->addDisplayGroup(
			array(
				'purchase_price',
				'sellprice_nomember',
				'sellprice_member',
				'barcode',
				'supplier',
				'bookable',
				'unbookable',
				'can_expire',
				'internal'
			),
			'stock_form'
		);
		$this->getDisplayGroup('stock_form')
		   	->setLegend('Stock Article')
		    ->setAttrib('id', 'stock_form')
		    ->removeDecorator('DtDdWrapper');

		$field = new Text('nb_black_and_white');
	    $field->setLabel('Number of B/W Pages')
	        ->setRequired()
	        ->addValidator('int')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

	    $field = new Text('nb_colored');
	    $field->setLabel('Number of Colored Pages')
	        ->setRequired()
	        ->addValidator('int')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

		$field = new Select('binding');
	    $field->setLabel('Binding')
	       	->setRequired()
			->setMultiOptions($this->_getBindings())
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

	    $field = new Checkbox('official');
	    $field->setLabel('Official')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

	    $field = new Checkbox('rectoverso');
	    $field->setLabel('Recto verso')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

		$field = new Select('front_color');
	    $field->setLabel('Front page color')
	      	->setRequired()
			->setMultiOptions($this->_getColors())
	       	->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);
		
		$this->addDisplayGroup(
		            array(
		                'nb_black_and_white',
		                'nb_colored',
		                'binding',
						'official',
						'rectoverso',
						'front_color'
		            ),
		            'internal_form'
		        );
		$this->getDisplayGroup('internal_form')
	    	->setLegend('Internal Article')
	        ->setAttrib('id', 'internal_form')
	        ->removeDecorator('DtDdWrapper');

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'textbooks_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

	private function _getSuppliers()
	{
		$suppliers = Registry::get('EntityManager')
            ->getRepository('Litus\Entity\Cudi\Supplier')
			->findAll();
		$supplierOptions = array();
		foreach($suppliers as $item)
			$supplierOptions[$item->getId()] = $item->getName();
		
		return $supplierOptions;
	}
	
	private function _getBindings()
	{
		$bindings = Registry::get('EntityManager')
	    	->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
			->findAll();
		$bindingOptions = array();
		foreach($bindings as $item)
			$bindingOptions[$item->getId()] = $item->getName();
		
		return $bindingOptions;
	}
	
	private function _getColors()
	{
		$colors = Registry::get('EntityManager')
	  		->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
			->findAll();
		$colorOptions = array();
		foreach($colors as $item)
			$colorOptions[$item->getId()] = $item->getName();
		
		return $colorOptions;
	}
}