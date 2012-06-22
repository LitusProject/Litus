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
	CudiBundle\Form\Admin\Stock\Deliveries\AddDirect as DeliveryForm,
	CudiBundle\Form\Admin\Stock\Orders\AddDirect as OrderForm,
	CudiBundle\Form\Admin\Stock\Update as StockForm,
	CudiBundle\Entity\Stock\Deliveries\Delivery,
	CudiBundle\Entity\Stock\PeriodValues\Delta;

/**
 * StockController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StockController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return;
            
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($period),
            $this->getParam('page')
        );
        
        return array(
            'period' => $period,
            'paginator' => $paginator,
            'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function searchAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return;
            
        switch($this->getParam('field')) {
        	case 'title':
        		$articles = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Stock\Period')
        			->findAllArticlesByPeriodAndTitle($period, $this->getParam('string'));
        		break;
        	case 'barcode':
        		$articles = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Stock\Period')
        			->findAllArticlesByPeriodAndBarcode($period, $this->getParam('string'));
        		break;
        	case 'supplier':
        		$articles = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Stock\Period')
        			->findAllArticlesByPeriodAndSupplier($period, $this->getParam('string'));
        		break;
        }
        
        $numResults = $this->getEntityManager()
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('search_max_results');
        
        array_splice($articles, $numResults);
        
        $result = array();
        foreach($articles as $article) {
        	$item = (object) array();
        	$item->id = $article->getId();
        	$item->title = $article->getMainArticle()->getTitle();
        	$item->supplier = $article->getSupplier()->getName();
        	$item->nbInStock = $article->getStockValue();
        	$item->nbNotDelivered = $period->getNbOrdered($article) - $period->getNbDelivered($article);
        	$item->nbNotDelivered = $item->nbNotDelivered < 0 ? 0 : $item->nbNotDelivered;
        	$item->nbReserved = $period->getNbBooked($article) + $period->getNbAssigned($article);
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
    }
    
    public function editAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return;
        
        if (!($article = $this->_getArticle()))
            return;
            
        $deliveryForm = new DeliveryForm($this->getEntityManager());
        $orderForm = new OrderForm($this->getEntityManager());
        $stockForm = new StockForm($article);
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

			if (isset($formData['updateStock'])) {
				if ($stockForm->isValid($formData)) {
					$delta = new Delta(
					    $this->getAuthentication()->getPersonObject(),
					    $article,
					    $period,
					    $formData['number'] - $article->getStockValue(),
					    $formData['comment']
					);
					$this->getEntityManager()->persist($delta);

					$article->setStockValue($formData['number']);
					
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
							'id' => $article->getId(),
						)
					);
					
					return;
				}
			} elseif (isset($formData['add_order'])) {
				if ($orderForm->isValid($formData)) {
					$this->getEntityManager()
						->getRepository('CudiBundle\Entity\Stock\Orders\Order')
						->addNumberByArticle($article, $formData['number'], $this->getAuthentication()->getPersonObject());
					
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
							'id' => $article->getId(),
						)
					);
					
					return;
				}
			} elseif (isset($formData['add_delivery'])) {
				if ($deliveryForm->isValid($formData)) {
					$delivery = new Delivery($article, $formData['number'], $this->getAuthentication()->getPersonObject());
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
							'id' => $article->getId(),
						)
					);
					
					return;
				}
			}
		}
        
        return array(
            'article' => $article,
            'period' => $period,
            'deliveryForm' => $deliveryForm,
            'orderForm' => $orderForm,
            'stockForm' => $stockForm,
        );
    }
    
    public function deltaAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return;
        
        if (!($article = $this->_getArticle()))
            return;
            
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\PeriodValues\Delta',
            $this->getParam('page'),
            array(
                'article' => $article,
                'period' => $period,
            ),
            array('timestamp' => 'DESC')
        );
        
        return array(
            'article' => $article,
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    private function _getArticle()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the sale article!'
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
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $item) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No sale article with the given id was found!'
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
    	
    	return $item;
    }
}