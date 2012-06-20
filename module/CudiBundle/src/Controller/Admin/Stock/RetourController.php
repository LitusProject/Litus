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
	CudiBundle\Entity\Stock\Deliveries\Retour,
	CudiBundle\Form\Admin\Stock\Deliveries\Add as AddForm,
	CudiBundle\Form\Admin\Stock\Deliveries\Retour as RetourForm;

/**
 * RetourController
 * 
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RetourController extends \CudiBundle\Component\Controller\ActionController
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
	            ->getRepository('CudiBundle\Entity\Stock\Deliveries\Retour')
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

	    $form = new RetourForm($this->getEntityManager());
		
		if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$article = $this->getEntityManager()
					->getRepository('CudiBundle\Entity\Sales\Article')
					->findOneById($formData['article_id']);
				
			    $item = new Retour($article, $formData['number'], $this->getAuthentication()->getPersonObject(), $formData['comment']);
				$this->getEntityManager()->persist($item);
				$this->getEntityManager()->flush();
				
				$this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The retour was successfully added!'
                    )
				);
				
				$this->redirect()->toRoute(
					'admin_stock_retour',
					array(
						'action' => 'supplier',
						'id'     => $article->getSupplier()->getId(),
					)
				);
			}
        }
        
        $retours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Deliveries\Retour')
            ->findAllByPeriod($period);
        
        array_splice($retours, 25);
        
        $suppliers = $this->getEntityManager()
        	->getRepository('CudiBundle\Entity\Supplier')
        	->findAll();
        
        return array(
        	'form' => $form,
        	'retours' => $retours,
        	'suppliers' => $suppliers,
        	'currentAcademicYear' => $academicYear,
        );
	}
	
	public function deleteAction()
	{
		$this->initAjax();
		
		if (!($retour = $this->_getRetour()))
		    return;
		
		$retour->getArticle()->addStockValue(-$retour->getNumber());
		$this->getEntityManager()->remove($retour);
		$this->getEntityManager()->flush();
		
		return array(
		    'result' => (object) array("status" => "success")
		);
	}
	
	private function _getRetour()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the retour!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock_retour',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $delivery = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Stock\Deliveries\Retour')
	        ->findOneById($this->getParam('id'));
		
		if (null === $delivery) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No retour with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_stock_retour',
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
				'admin_stock_retour',
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
				'admin_stock_retour',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $supplier;
	}
}
