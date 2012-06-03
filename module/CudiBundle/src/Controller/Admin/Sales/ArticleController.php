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
    CudiBundle\Form\Admin\Sales\Article\Add as AddForm,
    CudiBundle\Entity\Sales\Article as SaleArticle;

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
            'currentAcademicYear' => $academicYear,
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
        	        $formData['purchase_price'] * 100,
        	        $formData['sell_price'] * 100,
        	        $formData['bookable'],
        	        $formData['unbookable'],
        	        $supplier,
        	        $formData['can_expire'],
        	        $this->_getAcademicYear()
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
        
	}

    public function deleteAction()
	{
	}

	public function searchAction()
	{
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