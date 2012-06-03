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
    CudiBundle\Form\Admin\Sales\Article\Activate as ActivateForm,
    CudiBundle\Form\Admin\Sales\Article\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Article\Edit as EditForm,
    CudiBundle\Entity\Sales\Article as SaleArticle,
    CudiBundle\Entity\Sales\History;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $academicYear = $this->_getAcademicYear();
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\Article')
                ->findAllByAcademicYear($academicYear),
            $this->getParam('page')
        );
        
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();
                    
        return array(
            'academicYears' => $academicYears,
            'activeAcademicYear' => $academicYear,
            'currentAcademicYear' => $this->_getCurrentAcademicYear(),
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }

    public function addAction()
    {
        if (!($article = $this->_getArticle()))
            return;
        
        $form = new AddForm($this->getEntityManager());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $supplier = $this->getEntityManager()
        	    	->getRepository('CudiBundle\Entity\Supplier')
        	    	->findOneById($formData['supplier']);
        	    	
        	    $saleArticle = new SaleArticle(
        	        $article,
        	        $formData['barcode'],
        	        $formData['purchase_price'],
        	        $formData['sell_price'],
        	        $formData['bookable'],
        	        $formData['unbookable'],
        	        $supplier,
        	        $formData['can_expire'],
        	        $this->_getCurrentAcademicYear()
        	    );
        	    
        	    $this->getEntityManager()->persist($saleArticle);
        	    
        	    $this->getEntityManager()->flush();
        	    
        	    $this->flashMessenger()->addMessage(
        	        new FlashMessage(
        	            FlashMessage::SUCCESS,
        	            'SUCCESS',
        	            'The sale article was successfully created!'
        	        )
        	    );
        	    
        	    $this->redirect()->toRoute(
        	    	'admin_sales_article',
        	    	array(
        	    		'action' => 'manage'
        	    	)
        	    );
        	    
        	    return;
        	}
        }
        
        return array(
            'form' => $form,
            'article' => $article,
        );
    }
    
	public function editAction()
	{
		if (!($saleArticle = $this->_getSaleArticle()))
		    return;
        
        $form = new EditForm($this->getEntityManager(), $saleArticle);
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $history = new History($saleArticle);
        		$this->getEntityManager()->persist($history);
        		
        		$supplier = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Supplier')
        			->findOneById($formData['supplier']);
        			
        		$saleArticle->setBarcode($formData['barcode'])
        		    ->setPurchasePrice($formData['purchase_price'])
        		    ->setSellPrice($formData['sell_price'])
        		    ->setIsBookable($formData['bookable'])
        		    ->setIsUnbookable($formData['unbookable'])
        		    ->setSupplier($supplier)
        		    ->setCanExpire($formData['can_expire']);
        		
        		$this->getEntityManager()->flush();
        		        	    
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The sale article was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_sales_article',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return;
        	}
        }
        
        return array(
            'form' => $form,
            'article' => $saleArticle->getMainArticle(),
        );
	}
	
	public function activateAction()
	{
		if (!($saleArticle = $this->_getSaleArticle()))
		    return;
        
        $form = new ActivateForm($this->getEntityManager(), $saleArticle);
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $new = $saleArticle->duplicate();
        		
        		$supplier = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Supplier')
        			->findOneById($formData['supplier']);
        			
        		$new->setBarcode($formData['barcode'])
        		    ->setPurchasePrice($formData['purchase_price'])
        		    ->setSellPrice($formData['sell_price'])
        		    ->setIsBookable($formData['bookable'])
        		    ->setIsUnbookable($formData['unbookable'])
        		    ->setSupplier($supplier)
        		    ->setCanExpire($formData['can_expire'])
        		    ->setAcademicYear($this->_getCurrentAcademicYear());
        		
        		$this->getEntityManager()->persist($new);
        		$this->getEntityManager()->flush();
        		        	    
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The sale article was successfully activated for this academic year!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_sales_article',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return;
        	}
        }
        
        return array(
            'form' => $form,
            'article' => $saleArticle->getMainArticle(),
        );
	}

    public function deleteAction()
	{
	    $this->initAjax();
	    	    
		if (!($saleArticle = $this->_getSaleArticle()))
		    return;

        $saleArticle->setIsHistory(true);
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
	}

	public function searchAction()
	{
	    $this->initAjax();
	    
	    switch($this->getParam('field')) {
	    	case 'title':
	    		$articles = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Sales\Article')
	    			->findAllByTitleAndAcademicYear($this->getParam('string'), $this->_getAcademicYear());
	    		break;
	    	case 'author':
	    		$articles = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Sales\Article')
	    			->findAllByAuthorAndAcademicYear($this->getParam('string'), $this->_getAcademicYear());
	    		break;
	    	case 'publisher':
	    		$articles = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Sales\Article')
	    			->findAllByPublisherAndAcademicYear($this->getParam('string'), $this->_getAcademicYear());
	    		break;
	    }
	    
	    $numResults = $this->getEntityManager()
	    	->getRepository('CommonBundle\Entity\General\Config')
	    	->getConfigValue('search_max_results');
	    
	    array_splice($articles, $numResults);
	    
	    $result = array();
	    foreach($articles as $article) {
	    	$item = (object) array();
	    	$item->id = $article->getMainArticle()->getId();
	    	$item->title = $article->getMainArticle()->getTitle();
	    	$item->author = $article->getMainArticle()->getAuthors();
	    	$item->publisher = $article->getMainArticle()->getPublishers();
	    	$item->sellPrice = number_format($article->getSellPrice()/100, 2);
	    	$result[] = $item;
	    }
	    
	    return array(
	    	'result' => $result,
	    );
	}
    
    private function _getSaleArticle()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No ID was given to identify the article!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No article with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
    
    private function _getArticle()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No ID was given to identify the article!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No article with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_sales_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
}