<?php

namespace Admin;

use \Admin\Form\Order\Add as AddForm;
use \Admin\Form\Order\Edit as EditForm;
use \Admin\Form\Order\AddItem as AddItemForm;

use \Litus\Entity\Cudi\Stock\Order;
use \Litus\Entity\Cudi\Stock\OrderItem;
use \Litus\FlashMessenger\FlashMessage;

use \Zend\Pdf\PdfDocument;
use \Zend\Pdf\Page as PdfPage;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
			
		$this->view->orders = $this->_createPaginator(
            'Litus\Entity\Cudi\Stock\Order',
			array('supplier' => $supplier->getId())
        );
	}
	
	public function editAction()
	{
		$order = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Stock\Order')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $order)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);

		$this->view->order = $order;

		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $supplier = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Supplier')
					->findOneById($formData['supplier']);
				
				$order->setSupplier($supplier);
                 
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
		$form = new AddItemForm();
		
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
				$article = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Stock\StockItem')
					->findOneByBarcode($formData['stockArticle']);
				
				$item = $this->getEntityManager()
					->getRepository('Litus\Entity\Cudi\Stock\OrderItem')
					->addNumberByArticle($article, $formData['number']);
				$this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The order item was successfully added!'
                    )
				);
				
				$this->_redirect('edit', null, null, array('id' => $item->getOrder()->getId()));
			}
        }
	}
	
	public function deleteitemAction()
	{
		$item = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Stock\OrderItem')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $item || $item->getOrder()->isPlaced())
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
	
	public function placeAction()
	{
		$order = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Stock\Order')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $order || $order->isPlaced())
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
			
		$order->setDate(new \DateTime());
				
		$this->_redirect('edit', null, null, array('id' => $order->getId()));
	}
	
	public function printAction()
	{
		$order = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Stock\Order')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $order || !$order->isPlaced())
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$pdf = new PdfDocument();
		$page1 = $pdf->newPage(PdfPage::SIZE_A4);
		$pdf->pages[] = $page1;
		// TODO: print PDF
		
		$this->broker('layout')->disableLayout(); 
		$this->broker('viewRenderer')->setNoRender();
		$this->getResponse()
			->setHeader('Content-Type', 'application/pdf', true)
			->appendBody($pdf->render());
	}
}