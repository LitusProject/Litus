<?php

namespace Admin;

// use \Admin\Form\Textbook\Add as AddForm;

/*
 * Temporary use statements
 */

use Admin\Form\Textbook\Add;

use Admin\Form\Textbook\AddInternal;

use Litus\Entities\Cudi\Articles\StockArticles\Internal;

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

/**
 * 
 * This class controlls management and adding of textbooks.
 * @author Niels Avonds
 *
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
    	$form = new Add();

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
    			
    			// Add the new article to the database
    			
    			// Insert common information (between internal and external) in variables
    			
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
    			
    			if (!$formData['internal']) {
    				
    				$article = new External($title, $metaInfo, $purchase_price, $sellPrice, 
    					$sellPriceMembers, $barcode, $bookable, $unbookable);
    				
    			} else {
    				
    				// Insert additional information needed for internal textbooks in variables
    				$nrbwpages = $formData['nrbwpages'];
    				$nrcolorpages = $formData['nrcolorpages'];
    				$official = $formData['official'];
    				$rectoverso = $formData['rectoverso'];

    				$article = new Internal($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, 
    					$bookable, $unbookable, $nrbwpages, $nrcolorpages, $official, $rectoverso);

    			}
    			
    			$this->getEntityManager()->persist($metaInfo);
    			$this->getEntityManager()->persist($article);
    			
    			
    		}
			
    		// Make sure the validators and required flags are added again.
    		if (!$formData['internal']) {
    			 
    			foreach ($internal_form->getElements() as $formelement) {
    				if (array_key_exists ($formelement->getName(), $validators))
    					$formelement->setValidators($validators[$formelement->getName()]);
    					$formelement->setRequired($required[$formelement->getName()]);
    			}
    		}
    		
    	}
    }
}