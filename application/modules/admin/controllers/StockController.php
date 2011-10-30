<?php

namespace Admin;

use \Doctrine\ORM\EntityManager;

use \Admin\Form\Stock\AddOrder;

use \Litus\Entity\Cudi\Stock\Order;
use \Litus\FlashMessenger\FlashMessage;

/**
 *
 * This class controlls management of the stock.
 * @author Kristof Mariën
 *
 */
class StockController extends \Litus\Controller\Action
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
        $this->view->stock = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Stock\StockItem')->findAll();        
    }

	public function ordersAction()
	{
        $this->view->orders = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Stock\Order')->findAll();        		
	}
	
	public function addorderAction()
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
                $this->broker('flashmessenger')->addMessage(new FlashMessage(FlashMessage::SUCCESS, "SUCCESS", "The order was successfully created!"));
				$this->_redirect('orders');
			}
        }
	}
	
	public function deliveriesAction()
	{
        $this->view->deliveries = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Stock\Delivery')->findAll();        		
	}
}