<?php

namespace Admin;

use \Admin\Form\Delivery\Add as AddForm;

use \Litus\FlashMessenger\FlashMessage;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <ktistof.marien@litus.cc>
 */
class DeliveryController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('overview');
    }
	
	public function overviewAction()
	{
		$this->view->deliveries = $this->_createPaginator(
            'Litus\Entity\Cudi\Stock\Delivery'
        );
	}
	
	public function addAction()
	{
		$form = new AddForm();
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                
			}
        }
	}
}