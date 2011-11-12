<?php

namespace Admin;

use \Admin\Form\Delivery\Add as AddForm;

use \Litus\Entity\Cudi\Stock\DeliveryItem;

use \Litus\FlashMessenger\FlashMessage;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DeliveryController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('add');
    }
	
	public function addAction()
	{
		$this->view->deliveries = $this->getEntityManager()
			->getRepository('Litus\Entity\Cudi\Stock\DeliveryItem')
			->findLastNb(25);

		$form = new AddForm();
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
				$article = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Stock\StockItem')
					->findOneByBarcode($formData['stockArticle']);
				
                $item = new DeliveryItem($article, $formData['number']);
				$this->getEntityManager()->persist($item);
				
				$this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The delivery was successfully added!'
                    )
				);
				$this->_redirect('index');
			}
        }
	}
	
	public function deleteAction()
	{
		$item = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Stock\DeliveryItem')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $item)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
			
		$this->view->item = $item;
		
		if (null !== $this->getRequest()->getParam('confirm')) {
			if (1 == $this->getRequest()->getParam('confirm')) {
				$item->getArticle()->getStockItem()->addNumber(-$item->getNumber());
				$this->getEntityManager()->remove($item);

				$this->broker('flashmessenger')->addMessage(
            		new FlashMessage(
                		FlashMessage::SUCCESS,
                    	'SUCCESS',
                    	'The delivery was successfully removed!'
                	)
            	);
			};
            
			$this->_redirect('overview', null, null, array('id' => null));
        }
	}

	public function overviewAction()
	{
		$this->view->suppliers = $this->getEntityManager()
			->getRepository('Litus\Entity\Cudi\Supplier')
			->findAll();
	}
	
	public function supplierAction()
	{
		$supplier = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Supplier')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $supplier)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
			
		$this->view->deliveries = array();
	}
	
}