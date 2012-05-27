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
    CommonBundle\Component\Util\AcademicYear,
    CudiBundle\Entity\Articles\SubjectMap,
    CudiBundle\Form\Admin\Mapping\Add as AddForm;

/**
 * SubjectMapController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectMapController extends \CommonBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
	    if (!($article = $this->_getArticle()))
	        return;
	        
	    if (!($academicYear = $this->_getAcademicYear()))
	    	return;
	    
	    $form = new AddForm();
	    
	    if($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->post()->toArray();
	    	
	    	if ($form->isValid($formData)) {
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
                		'academicyear' => $academicYear->getCode(),
                	)
                );
	        }
	    }
		
		$mappings = $this->getEntityManager()
		    ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
		    ->findAllByArticleAndAcademicYear($article, $academicYear);
        
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();
            
        return array(
            'academicYears' => $academicYears,
            'currentAcademicYear' => $academicYear,
            'form' => $form,
            'article' => $article,
        	'mappings' => $mappings,
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
		if (!($mapping = $this->_getMapping()))
		    return;

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
            ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
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
    
    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
    		$start = AcademicYear::getStartOfAcademicYear();
    	} else {
    	    $start = AcademicYear::getDateTime($this->getParam('academicyear'));
    	}
    	$start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStartDate($start);
    	
    	if (null === $academicYear) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No academic year was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_study',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $academicYear;
    }
}