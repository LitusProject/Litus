<?php

namespace Admin;

use \Admin\Form\Textbook\Add as AddForm;

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
    	$form = new AddForm(array());
    	$this->view->form = $form;
    	
    	if($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    	
    		if($form->isValid($formData)) {
    			
    			// Add the newly inserted textbook to the database.
    			
    			
    			
    		}
    	}
    }
}