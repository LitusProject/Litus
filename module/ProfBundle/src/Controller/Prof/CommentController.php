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
    CudiBundle\Entity\Articles\Comment,
    ProfBundle\Form\Prof\Comment\Add as AddForm;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \ProfBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle()))
            return;
        
        $form = new AddForm();
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if($form->isValid($formData)) {
				$comment = new Comment($this->getAuthentication()->getPersonObject(), $article, $formData['text'], 'external');
				
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
					)
				);
				
				return;
			}
        }
                
    	return array(
    	    'article' => $article,
    	    'form' => $form,
    	    'comments' => $this->getEntityManager()
    	        ->getRepository('CudiBundle\Entity\Articles\Comment')
    	        ->findAllByArticleAndType($article, 'external')
    	);
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
        if (!($comment = $this->_getComment()))
    	    return;
    	    
    	if ($comment->getPerson()->getId() != $this->getAuthentication()->getPersonObject()->getId()) {
    		return array(
    		    'result' => (object) array("status" => "error")
    		);
    	}
    	
    	$this->getEntityManager()->remove($comment);
    	$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
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
            ->findOneById($id);
    	
    	$subjects = $this->getEntityManager()
    	    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
    	    ->findAllByProf($this->getAuthentication()->getPersonObject());
    	
    	foreach($subjects as $subject) {
    	    $mapping = $this->getEntityManager()
    	        ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
    	        ->findOneByArticleAndSubject($article, $subject->getSubject());
    	    
    	    if ($mapping)
    	        break;
    	}
    	
    	if (null === $article || null === $mapping) {
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
    
    private function _getComment()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the comment!'
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
    
        $comment = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Articles\Comment')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $comment || null === $this->_getArticle($comment->getArticle()->getId())) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No comment with the given id was found!'
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
    	
    	return $comment;
    }
}