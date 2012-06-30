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
    CudiBundle\Entity\Comments\Comment,
    CudiBundle\Form\Prof\Comment\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle()))
            return;
            
        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comments\Mapping')
            ->findByArticle($article);
                
        $form = new AddForm();
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$comment = new Comment(
				    $this->getEntityManager(),
				    $this->getAuthentication()->getPersonObject(),
				    $article,
				    $formData['text'],
				    'external'
				);
				
				$this->getEntityManager()->persist($comment);
                $this->getEntityManager()->flush();
                
				$this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The comment was successfully created!'
                    )
                );
                
				$this->redirect()->toRoute(
					'prof_comment',
					array(
						'action' => 'manage',
						'id' => $article->getId(),
						'language' => $this->getLanguage()->getAbbrev(),
					)
				);
				
				return;
			}
        }
                
    	return new ViewModel(
    	    array(
        	    'article' => $article,
        	    'form' => $form,
        	    'mappings' => $mappings,
        	)
    	);
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
        if (!($mapping = $this->_getCommentMapping()))
    	    return;
    	    
    	if ($mapping->getComment()->getPerson()->getId() != $this->getAuthentication()->getPersonObject()->getId()) {
    		return array(
    		    'result' => (object) array("status" => "error")
    		);
    	}
    	
    	$this->getEntityManager()->remove($mapping);
    	$this->getEntityManager()->flush();
        
        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
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
    
    private function _getCommentMapping()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'ERROR',
    		        'No id was given to identify the comment!'
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
    
        $mapping = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comments\Mapping')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $mapping || null === $this->_getArticle($mapping->getArticle()->getId())) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'ERROR',
    		        'No comment with the given id was found!'
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
    	
    	return $mapping;
    }
}