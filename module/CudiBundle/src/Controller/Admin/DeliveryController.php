<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
	CudiBundle\Entity\Stock\DeliveryItem,
	CudiBundle\Form\Admin\Delivery\Add as AddForm;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DeliveryAdminController extends \CommonBundle\Component\Controller\ActionController
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
			->getRepository('CudiBundle\Entity\Stock\DeliveryItem')
			->findLastNb(25);

		$form = new AddForm();
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
				$article = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\StockItem')
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
	        ->getRepository('CudiBundle\Entity\Stock\DeliveryItem')
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
			->getRepository('CudiBundle\Entity\Supplier')
			->findAll();
	}
	
	public function supplierAction()
	{
		$supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findOneById($this->getRequest()->getParam('id'));
		
		if (null == $supplier)
			throw new \Zend\Controller\Action\Exception('Page Not Found', 404);
		
		$this->view->supplier = $supplier;
		$this->view->deliveries = $this->_createPaginatorArray($this->getEntityManager()
			->getRepository('CudiBundle\Entity\Stock\DeliveryItem')
			->findAllBySupplier($supplier));
	}
	
}