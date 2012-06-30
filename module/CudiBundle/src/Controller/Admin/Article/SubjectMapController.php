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
 
namespace CudiBundle\Controller\Admin\Article;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Articles\SubjectMap,
    CudiBundle\Form\Admin\Article\Mapping\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * SubjectMapController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectMapController extends \CudiBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
	    if (!($article = $this->_getArticle()))
	        return;
	        
	    if (!($academicYear = $this->getAcademicYear()))
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
            
        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
                'article' => $article,
            	'mappings' => $mappings,
            )
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
		if (!($mapping = $this->_getMapping()))
		    return;

        $this->getEntityManager()->remove($mapping);
		$this->getEntityManager()->flush();
        
        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
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
}