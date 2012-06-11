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
    CudiBundle\Entity\Articles\External,
    CudiBundle\Entity\Articles\Internal,
    CudiBundle\Entity\Articles\History,
    CudiBundle\Entity\Articles\SubjectMap,
    CudiBundle\Form\Admin\Article\Add as AddForm,
    CudiBundle\Form\Admin\Article\Edit as EditForm;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ActionController
{

    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findAll(),
            $this->getParam('page')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        $academicYear = $this->_getAcademicYear();
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
				if ($formData['internal']) {
					$binding = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\Options\Binding')
						->findOneById($formData['binding']);

					$frontColor = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\Options\Color')
						->findOneById($formData['front_color']);

	                $article = new Internal(
						$formData['title'],
						$formData['author'],
						$formData['publisher'],
						$formData['year_published'],
						$formData['isbn'],
						$formData['url'],
						$formData['nb_black_and_white'],
                        $formData['nb_colored'],
                        $binding,
                        $formData['official'],
                        $formData['rectoverso'],
                        $frontColor,
                        $formData['front_text_colored'],
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
				
				$this->getEntityManager()->persist($article);
				
				$subject = $this->getEntityManager()
				    ->getRepository('SyllabusBundle\Entity\Subject')
				    ->findOneById($formData['subject_id']);
				    
				$mapping = $this->getEntityManager()
				    ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
				    ->findOneByArticleAndSubjectAndAcademicYear($article, $subject, $academicYear);
				
				if (null === $mapping) {
				    $mapping = new SubjectMap($article, $subject, $academicYear, $formData['mandatory']);
				    $this->getEntityManager()->persist($mapping);
				    $this->getEntityManager()->flush();
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
					'admin_article',
					array(
						'action' => 'manage'
					)
				);
				
				return;
        	}
        }
        
        return array(
            'form' => $form,
            'currentAcademicYear' => $academicYear,
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
        	    $history = new History($article);
				$this->getEntityManager()->persist($history);
        	    
           	    $article->setTitle($formData['title'])
        	        ->setAuthors($formData['author'])
        	        ->setPublishers($formData['publisher'])
        	        ->setYearPublished($formData['year_published'])
        	        ->setISBN($formData['isbn'])
        	        ->setURL($formData['url']);
        	    
				if ($article->isInternal()) {
					$binding = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\Options\Binding')
						->findOneById($formData['binding']);

					$frontPageColor = $this->getEntityManager()
						->getRepository('CudiBundle\Entity\Articles\Options\Color')
						->findOneById($formData['front_color']);
                    
                    $article->setNbBlackAndWhite($formData['nb_black_and_white'])
                    	->setNbColored($formData['nb_colored'])
                    	->setBinding($binding)
                    	->setIsOfficial($formData['official'])
                    	->setIsRectoVerso($formData['rectoverso'])
                    	->setFrontColor($frontPageColor)
                    	->setFrontPageTextColored($formData['front_text_colored'])
                    	->setIsPerforated($formData['perforated']);
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
                	'admin_article',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return;
        	}
        }
        
        $saleArticle = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneByArticleAndAcademicYear($article, $this->_getAcademicYear());
        
        return array(
            'form' => $form,
        	'article' => $article,
        	'saleArticle' => $saleArticle,
        );
	}

    public function deleteAction()
	{
	    $this->initAjax();
	    
		if (!($article = $this->_getArticle()))
		    return;

        $article->setIsHistory(true);
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
	    			->getRepository('CudiBundle\Entity\Article')
	    			->findAllByTitle($this->getParam('string'));
	    		break;
	    	case 'author':
	    		$articles = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Article')
	    			->findAllByAuthor($this->getParam('string'));
	    		break;
	    	case 'publisher':
	    		$articles = $this->getEntityManager()
	    			->getRepository('CudiBundle\Entity\Article')
	    			->findAllByPublisher($this->getParam('string'));
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
	    	$item->title = $article->getTitle();
	    	$item->author = $article->getAuthors();
	    	$item->publisher = $article->getPublishers();
	    	$item->yearPublished = $article->getYearPublished();
	    	$item->isInternal = $article->isInternal();
	    	$result[] = $item;
	    }
	    
	    return array(
	    	'result' => $result,
	    );
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
    			'admin_article',
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
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
}