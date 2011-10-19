<?php

namespace Admin\Form\Textbook;

use Zend\Form\Decorator\HtmlTag;

use Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\SubForm;
use \Zend\Form\Element\Text;
use Zend\Form\Element\Select;
use \Zend\Form\Element\Checkbox;

use Zend\Registry;


class AddInternal extends \Zend\Form\SubForm
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setDecorators(array('FormElements', new HtmlTag(array("tag" => "span", "id" => "internal_form", "style" => "display:none"))));
        // Make sure the attributes belonging to internal articles aren't seperated when posting
        $this->setIsArray(false);

        $nrbwpages = new Text('nbBlackAndWhite');
        $nrbwpages->setLabel('Number of black and white pages')
        ->setRequired()
        ->addValidator('int')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($nrbwpages);

        $nrcolorpages = new Text('nbColored');
        $nrcolorpages->setLabel('Number of colored pages')
        ->setRequired()
        ->addValidator('int')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($nrcolorpages);
        
		$bindings = Registry::get('EntityManager')
            ->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Binding')
			->findAll();
		$bindingOptions = array();
		foreach($bindings as $item) {
			$bindingOptions[] = array('key' => $item->getId(), 'value' => $item->getName());
		}
		
		$binding = new Select('binding');
        $binding->setLabel('Binding')
        	->setRequired()
			->setMultiOptions($bindingOptions)
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($binding);
         
        $official = new Checkbox('official');
        $official->setLabel('Official')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($official);
         
        $rectoverso = new Checkbox('rectoverso');
        $rectoverso->setLabel('Recto verso')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($rectoverso);

		$colors = Registry::get('EntityManager')
            ->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Color')
			->findAll();
		$colorOptions = array();
		foreach($colors as $item) {
			$colorOptions[] = array('key' => $item->getId(), 'value' => $item->getName());
		}
		
		$color = new Select('frontcolor');
        $color->setLabel('Front page color')
        	->setRequired()
			->setMultiOptions($colorOptions)
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($color);
    }
}