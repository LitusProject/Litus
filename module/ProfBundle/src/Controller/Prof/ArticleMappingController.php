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
    CudiBundle\Entity\ArticleSubjectMap,
    ProfBundle\Entity\Action\Mapping\Remove as RemoveAction,
    ProfBundle\Form\Prof\Mapping\Add as AddForm;

/**
 * ArticleMappingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleMappingController extends \ProfBundle\Component\Controller\ProfController
{
    public function addAction()
    {
        if (!($subject = $this->_getSubject()))
            return;
            
        $form = new AddForm();
        
        if($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->post()->toArray();
	    	
	    	if ($form->isValid($formData)) {
	    	    if (!($article = $this->_getArticle($formData['article_id'])))
	    	        return;
	    	         
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
                	'prof_subject',
                	array(
                		'action' => 'subject',
                		'id' => $subject->getId(),
                	)
                );
	        }
	    }
            
    	return array(
    	    'subject' => $subject,
    	    'form' => $form,
    	);
    }
    
    public function deleteAction()
    {
    	$this->initAjax();
    	
        if (!($mapping = $this->_getMapping()))
            return;
        
        $action = new RemoveAction($this->getAuthentication()->getPersonObject(), $mapping);
        $this->getEntityManager()->persist($action);
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
    			'prof_subject',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $mapping = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
            ->findOneById($this->getParam('id'));
    	
    	$mappingProf = $this->getEntityManager()
    	    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
    	    ->findOneBySubjectAndProf($mapping->getSubject(), $this->getAuthentication()->getPersonObject());
    	
    	if (null === $mapping || null === $mappingProf) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No mapping with the given id was found!'
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
    	
    	return $mapping;
    }
    
    private function _getSubject()
    {
        if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the subject!'
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
    
        $subject = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneById($this->getParam('id'));
    	
    	$mapping = $this->getEntityManager()
    	    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
    	    ->findOneBySubjectAndProf($subject, $this->getAuthentication()->getPersonObject());
    	
    	if (null === $subject || null === $mapping) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No subject with the given id was found!'
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
    	
    	return $subject;
    }
    
    private function _getArticle($id)
    {
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