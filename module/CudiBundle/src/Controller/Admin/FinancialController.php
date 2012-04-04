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
	CommonBundle\Entity\General\Bank\BankDevice\Amount as BankDeviceAmount,
	CommonBundle\Entity\General\Bank\CashRegister,
	CommonBundle\Entity\General\Bank\MoneyUnit\Amount as MoneyUnitAmount,
	CudiBundle\Entity\Sales\Session,
	CudiBundle\Form\Admin\Sale\CashRegisterEdit as CashRegisterEditForm,
	Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder;

/**
 * FinancialController
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 */
class FinancialController extends \CommonBundle\Component\Controller\ActionController
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
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function stockAction()
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
    
    public function supplierAction()
    {
    
    	$supplierRepository = $this->getEntityManager()
    		->getRepository( 'CudiBundle\Entity\Supplier' );
    		
    	$allSuppliers = $supplierRepository
    		->findAll();
    	
    	$currentSupplier = $supplierRepository
    		->findBy( array( 'id' => $this->getParam('id') ) );
    	
    	
    	if( null !== $this->getParam('id') && array() === $currentSupplier )
    	{
		    $this->flashMessenger()->addMessage(
		        new FlashMessage(
		            FlashMessage::ERROR,
		            'ERROR',
		            'The supplier "'.$this->getParam('id').'" was not found.'
		        )
		    );
		    
		    $this->redirect()->toRoute(
				'admin_financial',
				array(
					'action' => 'supplier'
				)
			);
		}
    		
		if( array() !== $currentSupplier )
		{
		
		    $deliveryItems = $this->getEntityManager()
		    	->createQueryBuilder()
				->select( 'di' )
				->from( 'CudiBundle\Entity\Stock\DeliveryItem', 'di' )
				->from( 'CudiBundle\Entity\Articles\StockArticles\Internal', 's' )
		    	->where( 's.id = di.article' )
		    	->andWhere( 's.supplier = ' . $this->getParam('id') )
		    	->orderBy( 'di.date','DESC' )
		    	->getQuery()
		    	->getResult();
		    	
		    	// TODO: add returnItems to this list ;-)
			
			$paginator = $this->paginator()->createFromArray(
				$deliveryItems,
				$this->getParam('page')
			);
		    
		    return array(
				'suppliers' => $allSuppliers,
				'showitems' => true,
				'paginator' => $paginator,
				'paginationControl' => $this->paginator()->createControl(true)
			);
        }
        else
        	$paginator = array();
		    
	    return array(
	    	'suppliers' => $allSuppliers,
	    	'showitems' => false
	    );
    }
    
}
