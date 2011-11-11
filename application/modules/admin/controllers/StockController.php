<?php

namespace Admin;

use \Litus\FlashMessenger\FlashMessage;

use \Admin\Form\Stock\Update as StockForm;
use \Admin\Form\Order\AddDirect as OrderForm;
use \Admin\Form\Delivery\AddDirect as DeliveryForm;

use \Litus\Entity\Cudi\Stock\DeliveryItem;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
		$this->view->stock = $this->_createPaginator(
            'Litus\Entity\Cudi\Stock\StockItem'
        );
    }

	public function editAction()
	{
		$item = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Stock\StockItem')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $item)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$stockForm = new StockForm();
		$stockForm->populate($item);
		
		$orderForm = new OrderForm();
		
		$deliveryForm = new DeliveryForm();

		$this->view->item = $item;
		$this->view->orderForm = $orderForm;
		$this->view->stockForm = $stockForm;
		$this->view->deliveryForm = $deliveryForm;
		
		if($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			
			if (isset($formData['updateStock'])) {
				if ($stockForm->isValid($formData)) {
					$item->setNumberInStock($formData['number']);
					
					$this->broker('flashmessenger')->addMessage(
	                    new FlashMessage(
	                        FlashMessage::SUCCESS,
	                        'SUCCESS',
	                        'The stock was successfully updated!'
	                    )
					);
					$this->_redirect('edit', null, null, array('id' => $item->getId()));
				}
			} elseif (isset($formData['addOrder'])) {
				if ($orderForm->isValid($formData)) {
					$this->getEntityManager()
						->getRepository('Litus\Entity\Cudi\Stock\OrderItem')
						->addNumberByArticle($item->getArticle(), $formData['number']);
					
					$this->broker('flashmessenger')->addMessage(
	                    new FlashMessage(
	                        FlashMessage::SUCCESS,
	                        'SUCCESS',
	                        'The order was successfully added!'
	                    )
					);
					$this->_redirect('edit', null, null, array('id' => $item->getId()));
				}
			} elseif (isset($formData['addDelivery'])) {
				if ($deliveryForm->isValid($formData)) {
					$delivery = new DeliveryItem($item->getArticle(), $formData['number']);
					$this->getEntityManager()->persist($delivery);

					$this->broker('flashmessenger')->addMessage(
		            	new FlashMessage(
		                	FlashMessage::SUCCESS,
		                    'SUCCESS',
		                    'The delivery was successfully added!'
		                )
					);
					$this->_redirect('edit', null, null, array('id' => $item->getId()));
				}
			}
		}
	}
}