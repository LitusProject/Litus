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
 * SubjectController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectController extends \ProfBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllByProf($this->getAuthentication()->getPersonObject());
        
    	return array(
    	    'subjects' => $subjects,
    	);
    }
    
    public function subjectAction()
    {
        if (!($subject = $this->_getSubject()))
            return;
        
        $allArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
            ->findAllBySubject($subject);
        
        $articles = array();
        foreach($allArticles as $article) {
            $removeAction = $this->getEntityManager()
                ->getRepository('ProfBundle\Entity\Action\Mapping\Remove')
                ->findOneByMapping($article);
            if (null === $removeAction)
                $articles[] = $article;
        }
          
        $profs = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllBySubject($subject);
        
        return array(
            'subject' => $subject,
            'articles' => $articles,
            'profs' => $profs,
        );
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
    
        $study = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $study) {
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
    	
    	return $study;
    }
}