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
 * StockController
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
		
		foreach($paginator as $item) {
			$item->setEntityManager($this->getEntityManager());
		}
		
		return array(
			'paginator' => $paginator,
			'paginationControl' => $this->paginator()->createControl(true)
		);
    }

	public function editAction()
	{
		$item = $this->_getItem();
		
		$stockForm = new StockForm($item);
		$orderForm = new OrderForm($this->getEntityManager());
		$deliveryForm = new DeliveryForm($this->getEntityManager());
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
			if (isset($formData['updateStock'])) {
				if ($stockForm->isValid($formData)) {
					$item->setNumberInStock($formData['number']);
					$this->getEntityManager()->flush();
					
					$this->flashMessenger()->addMessage(
	                    new FlashMessage(
	                        FlashMessage::SUCCESS,
	                        'SUCCESS',
	                        'The stock was successfully updated!'
	                    )
					);
					$this->redirect()->toRoute(
						'admin_stock',
						array(
							'action' => 'edit',
							'id' => $item->getId(),
						)
					);
				}
			} elseif (isset($formData['addOrder'])) {
				if ($orderForm->isValid($formData)) {
					$this->getEntityManager()
						->getRepository('CudiBundle\Entity\Stock\OrderItem')
						->addNumberByArticle($item->getArticle(), $formData['number']);
					$this->getEntityManager()->flush();
					
					$this->flashMessenger()->addMessage(
	                    new FlashMessage(
	                        FlashMessage::SUCCESS,
	                        'SUCCESS',
	                        'The order was successfully added!'
	                    )
					);
					$this->redirect()->toRoute(
						'admin_stock',
						array(
							'action' => 'edit',
							'id' => $item->getId(),
						)
					);
				}
			} elseif (isset($formData['addDelivery'])) {
				if ($deliveryForm->isValid($formData)) {
					$delivery = new DeliveryItem($item->getArticle(), $formData['number']);
					$this->getEntityManager()->persist($delivery);
					$this->getEntityManager()->flush();

					$this->flashMessenger()->addMessage(
		            	new FlashMessage(
		                	FlashMessage::SUCCESS,
		                    'SUCCESS',
		                    'The delivery was successfully added!'
		                )
					);
					$this->redirect()->toRoute(
						'admin_stock',
						array(
							'action' => 'edit',
							'id' => $item->getId(),
						)
					);
				}
			}
		}
		
		return array(
			'item' => $item,
			'stockForm' => $stockForm,
			'orderForm' => $orderForm,
			'deliveryForm' => $deliveryForm,
		);
	}

	public function searchAction()
	{
		$this->initAjax();
		
		switch($this->getParam('field')) {
			case 'title':
				$stock = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\StockItem')
					->findAllByArticleTitle($this->getParam('string'));
				break;
			case 'barcode':
				$stock = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\StockItem')
					->findAllByArticleBarcode($this->getParam('string'));
				break;
			case 'supplier':
				$stock = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Stock\StockItem')
					->findAllByArticleSupplierName($this->getParam('string'));
				break;
		}
		$result = array();
		foreach($stock as $stockItem) {
			$stockItem->setEntityManager($this->getEntityManager());
			
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
		
		return array(
			'result' => $result,
		);
	}
	
	private function _getItem()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the stock item!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $item = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Stock\StockItem')
	        ->findOneById($this->getParam('id'));
		
		if (null === $item) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No stock item with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		$item->setEntityManager($this->getEntityManager());
		
		return $item;
	}
}