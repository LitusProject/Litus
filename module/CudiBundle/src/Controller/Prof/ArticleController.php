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
 
namespace CudiBundle\Controller\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Article,
    CudiBundle\Entity\Articles\External,
    CudiBundle\Entity\Articles\Internal,
    CudiBundle\Entity\Articles\SubjectMap,
    CudiBundle\Entity\Prof\Action,
    CudiBundle\Form\Prof\Article\Add as AddForm,
    CudiBundle\Form\Prof\Article\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findAllByProf($this->getAuthentication()->getPersonObject());
                            
        return new ViewModel(
            array(
                'articles' => $articles,
            )
        );
    }
    
    public function addAction()
    {
        if (!($academicYear = $this->getAcademicYear()))
        	return;

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
						$formData['isbn'] != ''? $formData['isbn'] : null,
						$formData['url'],
						$formData['type'],
						$formData['downloadable'],
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
	                	$formData['isbn'] != ''? $formData['isbn'] : null,
	                	$formData['url'],
	                	$formData['type'],
	                	$formData['downloadable']
	           		);
				}
				
				$article->setIsProf(true);
					
                $this->getEntityManager()->persist($article);
                
                $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $article->getId(), 'add');
                $this->getEntityManager()->persist($action);
                
                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                    ->findOneBySubjectIdAndProfAndAcademicYear(
                        $formData['subject_id'],
                        $this->getAuthentication()->getPersonObject(),
                        $academicYear
                    );
                
                $mapping = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
                    ->findOneByArticleAndSubjectAndAcademicYear($article, $subject->getSubject(), $academicYear, true);
                
                if (null === $mapping) {
                    $mapping = new SubjectMap($article, $subject->getSubject(), $academicYear, $formData['mandatory']);
                    $mapping->setIsProf(true);
                    $this->getEntityManager()->persist($mapping);
                    
                    $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $mapping->getId(), 'add');
                    $this->getEntityManager()->persist($action);
                } else {
                    $actions = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Prof\Action')
                        ->findAllByEntityAndEntityIdAndAction('mapping', $mapping->getId(), 'remove');
                    foreach ($actions as $action)
                        $this->getEntityManager()->remove($action);
                }
				
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
                		'action' => 'manage',
        				'language' => $this->getLanguage()->getAbbrev(),
                	)
                );
                
                return;
        	}
        }
        
    	return new ViewModel(
    	    array(
    	        'form' => $form,
    	    )
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
                	    $duplicate->setISBN($formData['isbn'] != ''? $formData['isbn'] : null);
                	    $edited = true;
                	}
                	if ($article->getURL() != $formData['url']) {
                	    $duplicate->setURL($formData['url']);
                	    $edited = true;
                	}
                	if ($article->isDownloadable() != $formData['downloadable']) {
                	    $duplicate->setIsDownloadable($formData['downloadable']);
                	    $edited = true;
                	}
                	if ($article->getType() != $formData['type']) {
                	    $duplicate->setType($formData['type']);
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
				        ->setURL($formData['url'])
				        ->setIsDownloadable($formData['downloadable'])
				        ->setType($formData['type']);
				    				    
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
        	    		'action' => 'manage',
        	    		'language' => $this->getLanguage()->getAbbrev(),
        	    	)
        	    );
        	    
        	    return;
        	}
        }
        
    	return new ViewModel(
    	    array(
    	        'form' => $form,
    	        'article' => $article,
    	    )
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
        
        return new ViewModel(
            array(
        	    'result' => $result,
        	)
        );
    }
    
    private function _getArticle($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

    	if (null === $id) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'ERROR',
    		        'No id was given to identify the article!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_article',
    			array(
    				'action' => 'manage',
    				'language' => $this->getLanguage()->getAbbrev(),
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
    		        'ERROR',
    		        'No article with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_article',
    			array(
    				'action' => 'manage',
    				'language' => $this->getLanguage()->getAbbrev(),
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
}