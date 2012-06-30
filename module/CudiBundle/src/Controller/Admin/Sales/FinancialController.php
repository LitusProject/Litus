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
 
namespace CudiBundle\Controller\Admin\Sales;

use CommonBundle\Component\FlashMessenger\FlashMessage,
Zend\View\Model\ViewModel;

/**
 * FinancialController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 */
class FinancialController extends \CudiBundle\Component\Controller\ActionController
{
    public function salesAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Sales\Session',
            $this->getParam('page'),
            array(),
        	array('openDate' => 'DESC')
        );
        
        foreach($paginator as $item) {
        	$item->setEntityManager($this->getEntityManager());
        }
        
        return new ViewModel(
            array(
            	'paginator' => $paginator,
            	'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function stockAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return;
            
		$paginator = $this->paginator()->createFromArray(
		    $this->getEntityManager()
		        ->getRepository('CudiBundle\Entity\Stock\Period')
		        ->findAllArticlesByPeriod($period),
		    $this->getParam('page')
		);
        
        return new ViewModel(
            array(
                'period' => $period,
            	'paginator' => $paginator,
            	'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function suppliersAction()
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
        
        return new ViewModel(
            array(
            	'paginator' => $paginator,
            	'paginationControl' => $this->paginator()->createControl(true),
            	'suppliers' => $suppliers,
            )
        );
    }
    
    public function deliveriesAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return;
            
        $suppliers = $this->getEntityManager()
        	->getRepository('CudiBundle\Entity\Supplier')
        	->findAll();
        	
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Deliveries\Delivery',
            $this->getParam('page'),
            array(),
            array(
                'timestamp' => 'DESC',
            )
        );
        
        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'supplier' => $supplier,
            	'suppliers' => $suppliers,
            )
        );
    }
    
    public function retoursAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return;
            
        $suppliers = $this->getEntityManager()
        	->getRepository('CudiBundle\Entity\Supplier')
        	->findAll();
        	
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Deliveries\Retour',
            $this->getParam('page'),
            array(),
            array(
                'timestamp' => 'DESC',
            )
        );
        
        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'supplier' => $supplier,
            	'suppliers' => $suppliers,
            )
        );
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
    			'admin_sales_financial',
    			array(
    				'action' => 'suppliers'
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
    			'admin_sales_financial',
    			array(
    				'action' => 'suppliers'
    			)
    		);
    		
    		return;
    	}
    	
    	return $supplier;
    }
}