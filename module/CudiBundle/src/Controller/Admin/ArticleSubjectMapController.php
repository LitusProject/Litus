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
    CudiBundle\Entity\ArticleSubjectMap,
    CudiBundle\Form\Admin\Mapping\Add as AddForm;

/**
 * ArticleSubjectMapController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleSubjectMapController extends \CommonBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
	    $article = $this->_getArticle();
	    
	    $form = new AddForm();
	    
	    if($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->post()->toArray();
	    	
	    	if ($form->isValid($formData)) {
	    	    $subject = $this->getEntityManager()
	    	        ->getRepository('SyllabusBundle\Entity\Subject')
	    	        ->findOneById($formData['subject_id']);
	    	        
	    	    $mapping = $this->getEntityManager()
	    	        ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
	    	        ->findOneByArticleAndSubject($article, $subject);
	    	    
	    	    if (null === $mapping) {
    	    	    $mapping = new ArticleSubjectMap($article, $subject, $formData['mandatory']);
    	    	    $this->getEntityManager()->persist($mapping);
    	    	    $this->getEntityManager()->flush();
    	    	}
	    	    
	    	    $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The mapping was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_article_subject',
                	array(
                		'action' => 'manage',
                		'id' => $article->getId(),
                	)
                );
	        }
	    }
		
		$subjects = $this->getEntityManager()
		    ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
		    ->findAllByArticle($article);
        
        return array(
            'form' => $form,
            'article' => $article,
        	'subjects' => $subjects,
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
		$mapping = $this->_getMapping();

        $this->getEntityManager()->remove($mapping);
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
    }
    
    private function _getMapping()
    {
        if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the mapping!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article_subject',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No mapping with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article_subject',
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
    		        'No id was given to identify the article!'
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