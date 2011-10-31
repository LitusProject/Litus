<?php

namespace Admin;

use \Admin\Form\Stock\AddOrder;

use \Litus\Entity\Cudi\Stock\Order;
use \Litus\FlashMessenger\FlashMessage;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <ktistof.marien@litus.cc>
 */
class OrderController extends \Litus\Controller\Action
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
		$this->view->orders = $this->_createPaginator(
            'Litus\Entity\Cudi\Stock\Order'
        );
    }
	
	public function addAction()
	{
		$form = new AddOrder();
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
				
				$order = new Order($supplier, $formData['price']);
                 
                $this->getEntityManager()->persist($order);
                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The order was successfully created!'
                    )
				);
				$this->getEntityManager()->flush();
				
				$this->_redirect('edit', null, null, array('id' => $order->getId()));
			}
        }
	}
	
	public function editAction()
	{
		
	}
}