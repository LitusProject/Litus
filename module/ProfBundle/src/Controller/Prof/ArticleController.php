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
 
namespace ProfBundle\Controller\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Article,
    CudiBundle\Entity\Articles\MetaInfo,
    CudiBundle\Entity\Articles\Stub,
    CudiBundle\Entity\Articles\StockArticles\External,
    CudiBundle\Entity\Articles\StockArticles\Internal,
    ProfBundle\Entity\Action\Article\Add as AddAction,
    ProfBundle\Entity\Action\Article\Edit as EditAction,
    ProfBundle\Entity\Action\Article\Edit\Item as EditItem,
    ProfBundle\Form\Prof\Article\Add as AddForm,
    ProfBundle\Form\Prof\Article\Edit as EditForm;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \ProfBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findAllByProf($this->getAuthentication()->getPersonObject());
            
        foreach($articles as $article) {
            $this->applyEditsArticle($article);
        }
                            
        return array(
            'articles' => $articles,
        );
    }
    
    public function editAction()
    {
        if (!($article = $this->_getArticle()))
            return;
        
        $this->applyEditsArticle($article);
        
        $form = new EditForm($this->getEntityManager(), $article);
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    if ($article->isEnabled()) {
            	    $action = new EditAction($this->getAuthentication()->getPersonObject(), $article);
            	    $edited = false;
            	    
            	    if ($article->getTitle() != $formData['title']) {
                	    $this->getEntityManager()->persist(
                	        new EditItem($action, 'title', $formData['title'])
                	    );
                	    $edited = true;
                	}
                	
            	    if ($article->getMetaInfo()->getAuthors() != $formData['author']) {
                	    $this->getEntityManager()->persist(
                	        new EditItem($action, 'author', $formData['author'])
                	    );
                	    $edited = true;
                	}
                	if ($article->getMetaInfo()->getPublishers() != $formData['publisher']) {
                	    $this->getEntityManager()->persist(
                	        new EditItem($action, 'publisher', $formData['publisher'])
                	    );
                	    $edited = true;
                	}
                	if ($article->getMetaInfo()->getYearPublished() != $formData['year_published']) {
                	    $this->getEntityManager()->persist(
                	        new EditItem($action, 'year_published', $formData['year_published'])
                	    );
                	    $edited = true;
                	}
    				
    				if ($formData['stock']) {
    				    if ($formData['internal']) {
    				        if ($article->getBinding()->getId() != $formData['binding']) {
    				            $this->getEntityManager()->persist(
    				                new EditItem($action, 'binding', $formData['binding'])
    				            );
                        	    $edited = true;
    				        }
        					if ($article->isRectoVerso() != $formData['rectoverso']) {
            					$this->getEntityManager()->persist(
            					    new EditItem($action, 'rectoverso', $formData['rectoverso'])
            					);
                        	    $edited = true;
            				}
        				}
    				}
    				
    				if ($edited)
            	        $this->getEntityManager()->persist($action);
				} else {
				    $article->getMetaInfo()->setAuthors($formData['author'])
				        ->setPublishers($formData['publisher'])
				        ->setYearPublished($formData['year_published']);
				    
				    $article->setTitle($formData['title']);
				    
				    if ($formData['stock']) {
				        if ($formData['internal']) {
				            $article->setBinding(
    				                $this->getEntityManager()
        				                ->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
        				                ->findOneById($formData['binding'])
    				            )
				                ->setIsRectoVerso($formData['rectoverso']);
				    	}
				    }
				    $this->getEntityManager()->persist($article);
				}
				$this->getEntityManager()->flush();
        	    
        	    $this->flashMessenger()->addMessage(
        	        new FlashMessage(
        	            FlashMessage::SUCCESS,
        	            'SUCCESS',
        	            'The article was successfully updated!'
        	        )
        	    );
        	    
        	    $this->redirect()->toRoute(
        	    	'prof_article',
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
    
    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $metaInfo = new MetaInfo(
                    $formData['author'],
                    $formData['publisher'],
                    $formData['year_published']
                );
				
				if ($formData['stock']) {
					if ($formData['internal']) {
						$binding = $this->getEntityManager()
							->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
							->findOneById($formData['binding']);

		                $article = new Internal(
							$this->getEntityManager(),
		                	$formData['title'],
	                        $metaInfo,
	                        0,
	                        0,
	                        0,
		 					null,
	                        false,
	                        false,
	                        null,
	                        false,
							0,
	                        0,
	                        $binding,
	                        true,
	                        $formData['rectoverso'],
	                        null,
	                        false
		                );
					} else {
						$article = new External(
							$this->getEntityManager(),
		                	$formData['title'],
	                        $metaInfo,
	                        0,
	                        0,
	                        0,
							null,
	                        false,
	                        false,
	                        null,
	                        false
		           		);
					}
				} else {
					$article = new Stub(
	                	$formData['title'],
                        $metaInfo
	           		);
				}
				
				$article->setEnabled(false);
					
				$this->getEntityManager()->persist($metaInfo);
                $this->getEntityManager()->persist($article);
                
                $action = new AddAction($this->getAuthentication()->getPersonObject(), $article);
                $this->getEntityManager()->persist($action);
				
				$this->getEntityManager()->flush();
				
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully created!'
                    )
                );
                
                $this->redirect()->toRoute(
                	'prof_article',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return;
        	}
        }
        
    	return array(
    	    'form' => $form,
    	);
    }
    
    public function typeaheadAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findAllByProf($this->getAuthentication()->getPersonObject());
        
        $result = array();
        foreach($articles as $article) {
            $this->applyEditsArticle($article);
        	$item = (object) array();
        	$item->id = $article->getId();
        	$item->value = $article->getTitle() . ' - ' . $article->getMetaInfo()->getYearPublished();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
    }
    
    private function _getArticle($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

    	if (null === $id) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the article!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_subject',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneByIdAndProf($id, $this->getAuthentication()->getPersonObject());
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No article with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_subject',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
}