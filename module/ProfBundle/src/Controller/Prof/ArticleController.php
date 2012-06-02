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
    CudiBundle\Entity\Articles\External,
    CudiBundle\Entity\Articles\Internal,
    ProfBundle\Entity\Action,
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
                            
        return array(
            'articles' => $articles,
        );
    }
    
    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
				if ($formData['internal']) {
					$binding = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\Options\Binding')
						->findOneById($formData['binding']);

	                $article = new Internal(
						$formData['title'],
						$formData['author'],
						$formData['publisher'],
						$formData['year_published'],
						$formData['isbn'],
						$formData['url'],
						0,
                        0,
                        $binding,
                        true,
                        $formData['rectoverso'],
                        null,
                        false,
                        $formData['perforated']
	                );
				} else {
					$article = new External(
	                	$formData['title'],
	                	$formData['author'],
	                	$formData['publisher'],
	                	$formData['year_published'],
	                	$formData['isbn'],
	                	$formData['url']
	           		);
				}
				
				$article->setIsProf(true);
					
                $this->getEntityManager()->persist($article);
                
                $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $article->getId(), 'add');
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
    
    public function editAction()
    {
        if (!($article = $this->_getArticle()))
            return;
        
        $form = new EditForm($this->getEntityManager(), $article);
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    if (!$article->isProf()) {
                    $duplicate = $article->duplicate();
                    $duplicate->setIsProf(true);
            	    $edited = false;
            	    
            	    if ($article->getTitle() != $formData['title']) {
                	    $duplicate->setTitle($formData['title']);
                	    $edited = true;
                	}
                	
            	    if ($article->getAuthors() != $formData['author']) {
                	    $duplicate->setAuthors($formData['author']);
                	    $edited = true;
                	}
                	if ($article->getPublishers() != $formData['publisher']) {
                	    $duplicate->setPublishers($formData['publisher']);
                	    $edited = true;
                	}
                	if ($article->getYearPublished() != $formData['year_published']) {
                	    $duplicate->setYearPublished($formData['year_published']);
                	    $edited = true;
                	}
                	if ($article->getISBN() != $formData['isbn']) {
                	    $duplicate->setISBN($formData['isbn']);
                	    $edited = true;
                	}
                	if ($article->getURL() != $formData['url']) {
                	    $duplicate->setURL($formData['url']);
                	    $edited = true;
                	}
    				
				    if ($formData['internal']) {
				        if ($article->getBinding()->getId() != $formData['binding']) {
            	            $duplicate->setBinding($this->getEntityManager()
            	            	->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
            	            	->findOneById($formData['binding']));
                    	    $edited = true;
				        }
    					if ($article->isRectoVerso() != $formData['rectoverso']) {
            	            $duplicate->setIsRectoVerso($formData['rectoverso']);
                    	    $edited = true;
        				}
        				if ($article->isPerforated() != $formData['perforated']) {
            	            $duplicate->setIsPerforated($formData['perforated']);
        				    $edited = true;
        				}
    				}
    				
    				if ($edited) {
            	        $this->getEntityManager()->persist($duplicate);
                        $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $duplicate->getId(), 'edit', $article->getId());
            	        $this->getEntityManager()->persist($action);
            	    }
				} else {
				    $article->setAuthors($formData['author'])
				        ->setPublishers($formData['publisher'])
				        ->setYearPublished($formData['year_published'])
				        ->setTitle($formData['title'])
				        ->setISBN($formData['isbn'])
				        ->setURL($formData['url']);
				    				    
			        if ($formData['internal']) {
			            $article->setBinding(
				                $this->getEntityManager()
    				                ->getRepository('CudiBundle\Entity\Articles\Options\Binding')
    				                ->findOneById($formData['binding'])
				            )
			                ->setIsRectoVerso($formData['rectoverso'])
			                ->setIsPerforated($formData['perforated']);
			    	}
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
    
    public function typeaheadAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findAllByProf($this->getAuthentication()->getPersonObject());
        
        $result = array();
        foreach($articles as $article) {
        	$item = (object) array();
        	$item->id = $article->getId();
        	$item->value = $article->getTitle() . ' - ' . $article->getYearPublished();
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
    			'prof_article',
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
    			'prof_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
}