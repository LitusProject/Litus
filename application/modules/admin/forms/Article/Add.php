<?php

namespace Admin\Form\Article;

use Zend\Form\SubForm;

use Litus\Validator\Price as PriceValidator;
use Litus\Validator\Year as YearValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use Litus\Form\Admin\Decorator\FieldDecorator;

use Zend\Form\Form;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Select;
use Zend\Form\Element\Checkbox;

use Zend\Registry;

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
        $field->setLabel('Year published')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
			->addValidator('int')
        	->addValidator(new YearValidator());
        $this->addElement($field);
         
        $field = new Text('purchaseprice');
        $field->setLabel('Purchase price')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new PriceValidator());
        $this->addElement($field);
         
        $field = new Text('sellpricenomember');
        $field->setLabel('Sell price')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new PriceValidator());
        $this->addElement($field);
         
        $field = new Text('sellpricemember');
        $field->setLabel('Sell price (member)')
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

		$field = new Checkbox('canExpire');
        $field->setLabel('Can Expire')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Checkbox('internal');
        $field->setLabel('Internal article')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$this->addDisplayGroup(
			array(
				'title',
		        'author',
		        'publisher',
				'year_published',
				'purchaseprice',
				'sellpricenomember',
				'sellpricemember',
				'barcode',
				'supplier',
				'bookable',
				'unbookable',
				'canExpire',
				'internal'
		    ),
		    'article_form'
		);
		$this->getDisplayGroup('article_form')
		   	->setLegend('Article')
		    ->setAttrib('id', 'article_form')
		    ->removeDecorator('DtDdWrapper');

		$field = new Text('nbBlackAndWhite');
	    $field->setLabel('Number of black and white pages')
	        ->setRequired()
	        ->addValidator('int')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

	    $field = new Text('nbColored');
	    $field->setLabel('Number of colored pages')
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

		$field = new Select('frontcolor');
	    $field->setLabel('Front page color')
	      	->setRequired()
			->setMultiOptions($this->_getColors())
	       	->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);
		
		$this->addDisplayGroup(
		            array(
		                'nbBlackAndWhite',
		                'nbColored',
		                'binding',
						'official',
						'rectoverso',
						'frontcolor'
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
		foreach($suppliers as $item) {
			$supplierOptions[] = array('key' => $item->getId(), 'value' => $item->getName());
		}
		
		return $supplierOptions;
	}
	
	private function _getBindings()
	{
		$bindings = Registry::get('EntityManager')
	    	->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
			->findAll();
		$bindingOptions = array();
		foreach($bindings as $item) {
			$bindingOptions[] = array('key' => $item->getId(), 'value' => $item->getName());
		}
		
		return $bindingOptions;
	}
	
	private function _getColors()
	{
		$colors = Registry::get('EntityManager')
	  		->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
			->findAll();
		$colorOptions = array();
		foreach($colors as $item) {
			$colorOptions[] = array('key' => $item->getId(), 'value' => $item->getName());
		}
		
		return $colorOptions;
	}
}