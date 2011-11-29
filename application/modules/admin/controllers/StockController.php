<?php

namespace Admin;

use \Litus\FlashMessenger\FlashMessage;

use \Admin\Form\Stock\Update as StockForm;
use \Admin\Form\Order\AddDirect as OrderForm;
use \Admin\Form\Delivery\AddDirect as DeliveryForm;

use \Litus\Entity\Cudi\Stock\DeliveryItem;

use \Zend\Json\Json;

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
		$this->view->inlineScript()->appendFile($this->view->baseUrl('/_admin/js/cudi.searchDatabase.js'));
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

	public function searchAction()
	{
		$this->broker('contextSwitch')
            ->addActionContext('search', 'json')
            ->setAutoJsonSerialization(false)
            ->initContext();
        
        $this->broker('layout')->disableLayout();

        $json = new Json();

		$this->_initAjax();
		
		switch($this->getRequest()->getParam('field')) {
			case 'title':
				$stock = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Stock\StockItem')
					->findAllByArticleTitle($this->getRequest()->getParam('string'));
				break;
			case 'barcode':
				$stock = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Stock\StockItem')
					->findAllByArticleBarcode($this->getRequest()->getParam('string'));
				break;
			case 'supplier':
				$stock = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Stock\StockItem')
					->findAllByArticleSupplier($this->getRequest()->getParam('string'));
				break;
		}
		$result = array();
		foreach($stock as $stockItem) {
			$item = (object) array();
			$item->id = $stockItem->getId();
			$item->title = $stockItem->getArticle()->getTitle();
			$item->supplier = $stockItem->getArticle()->getSupplier()->getName();
			$item->numberInStock = $stockItem->getNumberInStock();
			$item->numberNotDelivered = $stockItem->getNumberNotDelivered();
			$item->numberQueueOrder = $stockItem->getNumberQueueOrder();
			$item->numberBookedAssigned = $stockItem->getNumberBooked() + $stockItem->getNumberAssigned();
			$result[] = $item;
		}
		echo $json->encode($result);
	}
}