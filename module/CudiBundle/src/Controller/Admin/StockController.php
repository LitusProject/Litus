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
	CudiBundle\Form\Admin\Delivery\AddDirect as DeliveryForm,
	CudiBundle\Form\Admin\Order\AddDirect as OrderForm,
	CudiBundle\Form\Admin\Stock\Update as StockForm,
	Zend\Json\Json;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StockController extends \CommonBundle\Component\Controller\ActionController
{
    
    public function manageAction()
	{
		$paginator = $this->paginator()->createFromEntity(
		    'CudiBundle\Entity\Stock\StockItem',
		    $this->getParam('page')
		);
		
		return array(
			'paginator' => $paginator,
			'paginationControl' => $this->paginator()->createControl()
		);
    }

	public function editAction()
	{
		$item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\StockItem')
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
						->getRepository('CudiBundle\Entity\Stock\OrderItem')
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
					->getRepository('CudiBundle\Entity\Stock\StockItem')
					->findAllByArticleTitle($this->getRequest()->getParam('string'));
				break;
			case 'barcode':
				$stock = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\StockItem')
					->findAllByArticleBarcode($this->getRequest()->getParam('string'));
				break;
			case 'supplier':
				$stock = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\StockItem')
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
			$item->versionNumber = $stockItem->getArticle()->getVersionNumber();
			$result[] = $item;
		}
		echo $json->encode($result);
	}
}