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
            $this->getParam('page'),
            array(),
        	array('id' => 'DESC')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function supplyAction()
    {
    
    	$supplierRepository = $this->getEntityManager()
    		->getRepository( 'CudiBundle\Entity\Supplier' );
    		
    	$allSuppliers = $supplierRepository
    		->findAll();
    	
    	$currentSupplier = $supplierRepository
    		->findBy( array( 'name' => $this->getParam('supplier') ) );
    	
    	if( array() === $currentSupplier && "" != $this->getParam('supplier') )
    	{
		    $this->flashMessenger()->addMessage(
		        new FlashMessage(
		            FlashMessage::ERROR,
		            'ERROR',
		            'The supplier "'.$this->getParam('supplier').'" was not found.'
		        )
		    ); // why does this flash message appear with delay? O_o It shouldn't!
		}
    
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\DeliveryItem',
            $this->getParam('page'),
            array(),
        	array('date' => 'DESC')
        ); // filter out delivery items delivered by other suppliers than the currentSupplier
        // ... but how? O_o
        
        return array(
        	'suppliers' => $allSuppliers,
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
}
