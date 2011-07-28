<?php

namespace Admin;

// use \Admin\Form\Textbook\Add as AddForm;

/*
 * Temporary use statements
 */

use Litus\Entities\Cudi\Articles\MetaInfo;

use Litus\Entities\Cudi\Articles\StockArticles\External;

use Zend\Form\Element;

use Zend\Form\Decorator\HtmlTag;
use Zend\Form\Decorator\Errors;

use Litus\Validator\PriceValidator;

use Zend\Validator\Regex;
use Zend\Validator\Int;

use Litus\Form\Decorator\DivSpanWrapper;
use Litus\Form\Decorator\FieldDecorator;

use Zend\Form\Form;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\SubForm;

/*
 * End of temporary
 */

class TextbookController extends \Litus\Controller\Action
{
    public function init()
    {

    }

    public function indexAction()
    {
    	$this->_forward('add');
    }
    
    public function addAction()
    {
    	$form = new Form;
    	
    	/*
    	 * Temporary until forms are fixed
    	 */
    	
    	$form->setAttrib('id', 'add')
    	->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'form'));
    	
    	// Create text fields that will contain information for the new course
    	$title = new Text('title');
    	$title->setLabel('Title')
    	->setRequired()
    	->setDecorators(array(new FieldDecorator()));
    	$form->addElement($title);
    	
    	$author = new Text('author');
    	$author->setLabel('Author')
    	->setRequired()
    	->setDecorators(array(new FieldDecorator()));
    	$form->addElement($author);
    	
    	$publisher = new Text('publisher');
    	$publisher->setLabel('Publisher')
    	->setRequired()
    	->setDecorators(array(new FieldDecorator()));
    	$form->addElement($publisher);
    	
    	$year = new Text('year_published');
    	$year->setLabel('Year published')
    	->setRequired()
    	->setDecorators(array(new FieldDecorator()))
    	->addValidator('int');
    	$form->addElement($year);
    	
    	$purchaseprice = new Text('purchaseprice');
    	$purchaseprice->setLabel('Purchase price')
    	->setRequired()
    	->setDecorators(array(new FieldDecorator()))
    	->addValidator(new PriceValidator());
    	$form->addElement($purchaseprice);
    	
    	$sellprice = new Text('sellpricenomember');
    	$sellprice->setLabel('Sell price')
    	->setRequired()
    	->setDecorators(array(new FieldDecorator()))
    	->addValidator(new PriceValidator());
    	$form->addElement($sellprice);
    	
    	$sellpricemember = new Text('sellpricemember');
    	$sellpricemember->setLabel('Sell price (member)')
    		->setRequired()
    		->setDecorators(array(new FieldDecorator()))
    		->addValidator(new PriceValidator());
    	$form->addElement($sellpricemember);
    	
    	// TODO: readd when db is ready for it
//     	$supplier = new Text('supplier');
//     	$supplier->setLabel('Supplier')
//     		->setRequired()
//     		->setDecorators(array(new FieldDecorator()));
//     	$form->addElement($supplier);
    	
    	$bookable = new Checkbox('bookable');
    	$bookable->setLabel('Bookable')
    		->setDecorators(array(new FieldDecorator()));
    	$form->addElement($bookable);
    	
    	$unbookable = new Checkbox('unbookable');
    	$unbookable->setLabel('Unbookable')
    	->setDecorators(array(new FieldDecorator()));
    	$form->addElement($unbookable);
    	
    	$internal = new Checkbox('internal');
    	$internal->setLabel('Internal article')
    		->setDecorators(array(new FieldDecorator()));
    	$internal->setAttrib('onclick', "toggle_visibility()");
    	$form->addElement($internal);
    	
    	
    	
    	// TODO: barcode: see zend barcode validator?
    	// TODO: internal articles

    	
    	/*
    	 * Internal subform
    	 */
    	
    	$internal_form = new SubForm();
		$internal_form->setDecorators(array('FormElements', new HtmlTag(array("tag" => "span", "id" => "internal_form", "style" => "display:none"))));
		// Make sure the attributes belonging to internal articles aren't seperated when posting
		$internal_form->setIsArray(false);
		
    	$nrbwpages = new Text('nrbwpages');
    	$nrbwpages->setLabel('Number of black and white pages')
    		->setRequired()
    		->setDecorators(array(new FieldDecorator()));
    	$internal_form->addElement($nrbwpages);

    	$nrcolorpages = new Text('nrcolorpages');
    	$nrcolorpages->setLabel('Number of colored pages')
    		->setRequired()
    		->setDecorators(array(new FieldDecorator()));
    	$internal_form->addElement($nrcolorpages);
    	
    	$binding = new Text('binding');
    	$binding->setLabel('Binding')
    		->setRequired()
    		->setDecorators(array(new FieldDecorator()));
    	$internal_form->addElement($binding);
    	
    	$official = new Checkbox('official');
    	$official->setLabel('Official')
    		->setDecorators(array(new FieldDecorator()));
    	$internal_form->addElement($official);
    	
    	$rectoverso = new Checkbox('rectoverso');
    	$rectoverso->setLabel('Recto verso')
    		->setDecorators(array(new FieldDecorator()));
    	$internal_form->addElement($rectoverso);
    	
    	$frontcolor = new Text('frontcolor');
    	$frontcolor->setLabel('Front page color')
    		->setRequired()
    		->setDecorators(array(new FieldDecorator()));
    	$internal_form->addElement($frontcolor);
		
    	$form->addSubForm($internal_form, 'internalform');
    	
    	/*
    	 * End of internal subform
    	 */

    	// Create the button
    	 
    	$submit = new Submit('submit');
    	$submit->setLabel('Add');
    	$submit->setDecorators(array(
    		'ViewHelper'
    	));
    	$form->addElement($submit);
    	
    	/*
    	* End of temporary code
    	*/
    	
    	$this->view->form = $form;
    	
    	if($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		/*
    		 * If this isn't an internal article, remove all the validators and required flags from that subform.
    		 * 
    		 * Remember them in arrays to restore it later.
    		 */
    		$validators = array();
    		$required = array();
    		if (!$formData['internal']) {
    			
    		    foreach ($internal_form->getElements() as $formelement) {
    				$validators[$formelement->getName()] = $formelement->getValidators();
    				$required[$formelement->getName()] = $formelement->isRequired();
    				$formelement->clearValidators();
    				$formelement->setRequired(false);
    			}
    		}
    		
    		
    		
    		if($form->isValid($formData)) {
    			
    			if (!$formData['internal']) {
    				
    				$authors = $formData['author'];
    				$publishers = $formData['publisher'];
    				$yearPublished = $formData['year_published'];
    				
    				$metaInfo = new MetaInfo($authors, $publishers, $yearPublished);
    				
    				$title = $formData['title'];
    				$purchase_price = $formData['purchaseprice'];
    				$sellPrice = $formData['sellpricenomember'];
    				$sellPriceMembers = $formData['sellpricemember'];
    				$barcode = 0; // TODO barcode
    				$bookable = $formData['bookable'];
    				$unbookable = $formData['unbookable'];
    				
    				$article = new External($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable);
    				
    				
    			} else {
    				// Add the newly inserted textbook to the database.
    				echo 'VALIDATED! ';
    				var_dump($formData);
    			}
    			
    			
    		}
			
    		/*
    		 * Make sure the validators and required flags are added again.
    		 */
    		if (!$formData['internal']) {
    			 
    			foreach ($internal_form->getElements() as $formelement) {
    				if (array_key_exists ($formelement->getName(), $validators))
    					$formelement->setValidators($validators[$formelement->getName()]);
    					$formelement->setRequired($required[$formelement->getName()]);
    			}
    		}
    		
    	}
    }
    
    private function setRequireForTextsInForm($form, $required = true) {
    	
    	foreach ($form->getElements() as $formelement) {
    		 
    		if ($formelement->getType() == 'Zend\Form\Element\Text') {
    			$formelement->setRequired($required);
    		}
    		 
    	}
    	
    }
}