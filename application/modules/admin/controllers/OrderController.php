<?php

namespace Admin;

use \Admin\Form\Order\Add as AddForm;
use \Admin\Form\Order\Edit as EditForm;
use \Admin\Form\Order\AddItem as AddItemForm;

use \Litus\Util\File as FileUtil;
use \Litus\Cudi\OrderPdfGenerator;
use \Litus\Cudi\OrderXmlGenerator;
use \Litus\Entity\Cudi\Stock\Order;
use \Litus\Entity\Cudi\Stock\OrderItem;
use \Litus\FlashMessenger\FlashMessage;

use \Zend\Pdf\PdfDocument;
use \Zend\Pdf\Page as PdfPage;
use \Zend\Registry;


use \Litus\Util\Xml\XmlGenerator;
use \Litus\Util\Xml\XmlObject;
use \Litus\Util\TmpFile;

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

		$contextSwitch = $this->broker('contextSwitch');
        $contextSwitch->setContext(
	            'pdf',
	            array(
	                 'headers' => array(
	                     'Content-type' => 'application/pdf',
	                     'Pragma' => 'public',
	                     'Cache-Control' => 'private, max-age=0, must-revalidate'
	                 )
	            )
	        )->setContext(
                'zip',
                array(
                     'headers' => array(
                         'Content-type' => 'application/zip',
                         'Pragma' => 'public',
                         'Cache-Control' => 'private, max-age=0, must-revalidate'
                     )
                )
            );

        $contextSwitch->setActionContext('pdf', 'pdf')
        	->setActionContext('export', 'zip')
            ->initContext();
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
			
		$this->view->supplier = $supplier;
		$this->view->orders = $this->_createPaginator(
            'Litus\Entity\Cudi\Stock\Order',
			array('supplier' => $supplier->getId())
        );
	}
	
	public function editAction()
	{
		$this->view->inlineScript()->appendFile($this->view->baseUrl('/_admin/js/downloadFile.js'));
		
		$order = $this->getEntityManager()
            ->getRepository('Litus\Entity\Cudi\Stock\Order')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $order)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);

		$this->view->order = $order;
	}
	
	public function addAction()
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
	
	public function pdfAction()
	{
		$this->broker('layout')->disableLayout(); 
		$this->broker('viewRenderer')->setNoRender();
		
		$order = $this->getEntityManager()
	        ->getRepository('Litus\Entity\Cudi\Stock\Order')
	    	->findOneById($this->getRequest()->getParam('id'));
	
		if (null == $order || !$order->isPlaced())
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$document = new OrderPdfGenerator($order);
		$document->generate();

		// TODO: remove content type (must be in init)
		$this->getResponse()->setHeader(
			'Content-Disposition', 'inline; filename="order.pdf"'
		)->setHeader(
			'Content-type', 'application/pdf'
		);
		
		$this->getResponse()->setHeader('Content-Length', filesize($file));

		readfile($file);
	}
	
	public function exportAction()
	{
		$this->broker('layout')->disableLayout(); 
		$this->broker('viewRenderer')->setNoRender();
		
		$order = $this->getEntityManager()
			->getRepository('Litus\Entity\Cudi\Stock\Order')
			->findOneById($this->getRequest()->getParam('id'));
			
		if (null == $order || !$order->isPlaced())
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$document = new OrderXmlGenerator($order);
		
		
		// TODO: remove content type (must be in init)
		$this->getResponse()->setHeader(
			'Content-Disposition', 'inline; filename="order.zip"'
		)->setHeader(
			'Content-type', 'application/zip'
		);
		
		$archive = new TmpFile();
		$document->generateArchive($archive);
		readfile($archive->getFileName());
	}
}