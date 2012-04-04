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

use CommonBundle\Component\FlashMessenger\FlashMessage;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \ProfBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $articles = array();
        
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllByProf($this->getAuthentication()->getPersonObject());
        
        foreach($subjects as $subject) {
            $allArticles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
                ->findAllBySubject($subject->getSubject());
            
            foreach($allArticles as $article) {
                $removeAction = $this->getEntityManager()
                    ->getRepository('ProfBundle\Entity\Action\Mapping\Remove')
                    ->findOneByMapping($article);
                if (null === $removeAction && (!$article->getArticle()->isInternal() || $article->getArticle()->isOfficial()))
                    $articles[] = $article;
            }
        }
        
        return array(
            'articles' => $articles,
        );
    }
    
    public function editAction()
    {
        if (!($article = $this->_getArticle()))
            return;
        
    	return array();
    }
    
    public function addAction()
    {
    	return array();
    }
    
    public function typeaheadAction()
    {
        $articles = array();
        
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllByProf($this->getAuthentication()->getPersonObject());
        
        foreach($subjects as $subject) {
            $allArticles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
                ->findAllBySubject($subject->getSubject());
            
            foreach($allArticles as $article) {
                $removeAction = $this->getEntityManager()
                    ->getRepository('ProfBundle\Entity\Action\Mapping\Remove')
                    ->findOneByMapping($article);
                if (null === $removeAction && (!$article->getArticle()->isInternal() || $article->getArticle()->isOfficial()))
                    $articles[] = $article->getArticle();
            }
        }
        
        $result = array();
        foreach($articles as $article) {
        	$item = (object) array();
        	$item->id = $article->getId();
        	$item->value = $article->getTitle() . ' - ' . $article->getMetaInfo()->getYearPublished();
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
            ->findOneById($this->getParam('id'));
    	
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