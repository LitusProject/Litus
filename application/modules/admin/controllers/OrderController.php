<?php

namespace Admin;

use \Admin\Form\Order\Add as AddForm;
use \Admin\Form\Order\Edit as EditForm;
use \Admin\Form\Order\AddItem as AddItemForm;

use \Litus\Entity\Cudi\Stock\Order;
use \Litus\Entity\Cudi\Stock\OrderItem;
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
		$form = new AddForm();
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
		$order = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Stock\Order')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $order)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$form = new EditForm();
		$form->populate($order);

        $this->view->form = $form;
		$this->view->order = $order;

		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
				
				$order->setSupplier($supplier)
					->setPrice($formData['price']);
                 
                $this->_addDirectFlashMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The order was successfully updated!'
                    )
				);
			}
        }
	}
	
	public function additemAction()
	{
		$order = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Stock\Order')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $order)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
			
		$form = new AddItemForm();
		
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
				$article = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Articles\StockArticles\External')
					->findOneByBarcode($formData['stockArticle']);
				if (null == $article) {
					$article = $this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Articles\StockArticles\Internal')
						->findOneByBarcode($formData['stockArticle']);
				}
				
				$item = new OrderItem($article, $order, $formData['number']);
                 
                $this->getEntityManager()->persist($item);
                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The order item was successfully created!'
                    )
				);
				
				$this->_redirect('edit', null, null, array('id' => $order->getId()));
			}
        }
	}
	
	public function deleteitemAction()
	{
		$item = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Stock\OrderItem')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $item)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
			
		$this->view->item = $item;
		
		if (null !== $this->getRequest()->getParam('confirm')) {
			if (1 == $this->getRequest()->getParam('confirm')) {
				$this->getEntityManager()->remove($item);

				$this->broker('flashmessenger')->addMessage(
            		new FlashMessage(
                		FlashMessage::SUCCESS,
                    	'SUCCESS',
                    	'The article was successfully removed!'
                	)
            	);
			};
            
			$this->_redirect('edit', null, null, array('id' => $item->getOrder()->getId()));
        }
	}
}