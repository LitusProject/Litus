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
 
namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    SyllabusBundle\Entity\SubjectProfMap,
    SyllabusBundle\Form\Admin\Prof\Add as AddForm;

/**
 * ProfController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CommonBundle\Component\Controller\ActionController
{
    public function addAction()
    {
        if (!($subject = $this->_getSubject()))
        	return;
        	
        $form = new AddForm();
        
        if($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->post()->toArray();
	    	
	    	if ($form->isValid($formData)) {
	    	    $docent = $this->getEntityManager()
	    	        ->getRepository('CommonBundle\Entity\Users\People\Academic')
	    	        ->findOneById($formData['prof_id']);
	    	        
	    	    $mapping = $this->getEntityManager()
	    	        ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
	    	        ->findOneBySubjectAndProf($subject, $docent);
	    	    
	    	    if (null === $mapping) {
    	    	    $mapping = new SubjectProfMap($subject, $docent);
    	    	    $this->getEntityManager()->persist($mapping);
    	    	    $this->getEntityManager()->flush();
    	    	}
	    	    
	    	    $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The docent was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_subject',
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
        	
        $this->getEntityManager()->remove($mapping);
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
    }
    
    public function typeaheadAction()
    {
        $docents = array_merge(
            $this->getEntityManager()
            	->getRepository('CommonBundle\Entity\Users\People\Academic')
            	->findAllByName($this->getParam('string')),
            $this->getEntityManager()
            	->getRepository('CommonBundle\Entity\Users\People\Academic')
            	->findAllByUniversityIdentification($this->getParam('string'))
        );
        	
        $result = array();
        foreach($docents as $docent) {
        	$item = (object) array();
        	$item->id = $docent->getId();
        	$item->value = $docent->getUniversityIdentification() . ' - ' . $docent->getFullName();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
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
    			'admin_study',
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
    			'admin_study',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $study;
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
    			'admin_study',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $mapping) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No mapping with the given id was found!'
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
    	
    	return $mapping;
    }
}