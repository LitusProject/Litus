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
    CudiBundle\Entity\Stock\Period;

/**
 * PeriodController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PeriodController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Period',
            $this->getParam('page'),
            array(),
            array('startDate' => 'DESC')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl()
        );
    }
    
    public function viewAction()
    {
        if (!($period = $this->_getPeriod()))
            return;
            
        $period->setEntityManager($this->getEntityManager());
            
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\StockItem',
            $this->getParam('page')
        );
        
        foreach($paginator as $item) {
        	$item->setEntityManager($this->getEntityManager());
        }
        
        return array(
            'period' => $period,
            'paginator' => $paginator,
            'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function newAction()
    {
        $open = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOpen();
        if ($open)
            $open->close();
        
        $new = new Period($this->getAuthentication()->getPersonObject());
        $this->getEntityManager()->persist($new);
        
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The period was succesfully created.'
            )
        );
        
        $this->redirect()->toRoute(
        	'admin_period',
        	array(
        		'action' => 'manage'
        	)
        );
        
        return;
    }
    
    public function searchAction()
    {
    	$this->initAjax();
    	
    	if (!($period = $this->_getPeriod()))
    	    return;
    	    
    	$period->setEntityManager($this->getEntityManager());
    	
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
    		$item->delivered = $period->getNbDelivered($stockItem->getArticle());
    		$item->ordered = $period->getNbOrdered($stockItem->getArticle());
    		$item->sold = $period->getNbSold($stockItem->getArticle());
    		$item->versionNumber = $stockItem->getArticle()->getVersionNumber();
    		$result[] = $item;
    	}
    	
    	return array(
    		'result' => $result,
    	);
    }
    
    private function _getPeriod()
    {
        if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No ID was given to identify the period!'
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
    
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $period) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No period with the given id was found!'
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
    	
    	return $period;
    }
}