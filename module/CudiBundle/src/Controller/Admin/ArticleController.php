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
    CudiBundle\Entity\Articles\Stub,
    CudiBundle\Form\Admin\Article\Add as AddForm,
	Doctrine\ORM\EntityManager;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CommonBundle\Component\Controller\ActionController
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
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    if ($formData['stock']) {
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
				} else {
					$article = new Stub(
	                	$formData['title'],
	                	$formData['author'],
	                	$formData['publisher'],
	                	$formData['year_published'],
	                	$formData['isbn'],
	                	$formData['url']
	           		);
				}
				
				$this->getEntityManager()->persist($article);
				
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
        );
    }
    
	public function editAction()
	{
		if (!($article = $this->_getArticle()))
		    return;
        
        return array(
        	'article' => $article,
        );
	}

    public function deleteAction()
	{
	
	}

	public function searchAction()
	{
	    
	}
	
	public function newVersionAction()
	{
	    
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