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
 
namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\FlashMessenger\FlashMessage,
	CudiBundle\Entity\Stock\Deliveries\Delivery,
	CudiBundle\Form\Admin\Stock\Deliveries\Add as AddForm;

/**
 * DeliveryController
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DeliveryController extends \CudiBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
		$paginator = $this->paginator()->createFromEntity(
		    'CudiBundle\Entity\Supplier',
		    $this->getParam('page'),
		    array(),
		    array(
		        'name' => 'ASC'
		    )
		);
		
		$suppliers = $this->getEntityManager()
			->getRepository('CudiBundle\Entity\Supplier')
			->findAll();
		
		return array(
			'paginator' => $paginator,
			'paginationControl' => $this->paginator()->createControl(true),
			'suppliers' => $suppliers,
		);
	}
	
	public function supplierAction()
	{
	    if (!($supplier = $this->_getSupplier()))
	        return;
	    
	    if (!($period = $this->getActiveStockPeriod()))
	        return;
	        
	    $paginator = $this->paginator()->createFromArray(
	        $this->getEntityManager()
	            ->getRepository('CudiBundle\Entity\Stock\Deliveries\Delivery')
	            ->findAllBySupplierAndPeriod($supplier, $period),
	        $this->getParam('page')
	    );
	    
	    $suppliers = $this->getEntityManager()
	    	->getRepository('CudiBundle\Entity\Supplier')
	    	->findAll();
	    
	    return array(
	    	'supplier' => $supplier,
	    	'paginator' => $paginator,
	    	'paginationControl' => $this->paginator()->createControl(),
	    	'suppliers' => $suppliers,
	    );
	}	
	
	public function addAction()
	{
	    if (!($period = $this->getActiveStockPeriod()))
	        return;
	        
	    $academicYear = $this->getAcademicYear();
	        
		$form = new AddForm($this->getEntityManager());
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$article = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Article')
					->findOneById($formData['article_id']);
				
			    $item = new Delivery($article, $formData['number'], $this->getAuthentication()->getPersonObject());
				$this->getEntityManager()->persist($item);
				$this->getEntityManager()->flush();
				
				$this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The delivery was successfully added!'
                    )
				);
				
				$this->redirect()->toRoute(
					'admin_stock_delivery',
					array(
						'action' => 'supplier',
						'id'     => $article->getSupplier()->getId(),
					)
				);
			}
        }
        
        $deliveries = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Deliveries\Delivery')
            ->findAllByPeriod($period);
        
        array_splice($deliveries, 25);
        
        $suppliers = $this->getEntityManager()
        	->getRepository('CudiBundle\Entity\Supplier')
        	->findAll();
        
        return array(
        	'form' => $form,
        	'deliveries' => $deliveries,
        	'suppliers' => $suppliers,
        	'currentAcademicYear' => $academicYear,
        );
	}
	
	public function deleteAction()
	{
		$this->initAjax();
		
		if (!($delivery = $this->_getDelivery()))
		    return;
		
		$delivery->getArticle()->addStockValue(-$delivery->getNumber());
		$this->getEntityManager()->remove($delivery);
		$this->getEntityManager()->flush();
		
		return array(
		    'result' => (object) array("status" => "success")
		);
	}
	
	private function _getDelivery()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the delivery!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock_delivery',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $delivery = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Stock\Deliveries\Delivery')
	        ->findOneById($this->getParam('id'));
		
		if (null === $delivery) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No delivery with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock_delivery',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $delivery;
	}
	
	private function _getSupplier()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the supplier!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock_delivery',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $supplier = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Supplier')
	        ->findOneById($this->getParam('id'));
		
		if (null === $supplier) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No supplier with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock_delivery',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $supplier;
	}
}
