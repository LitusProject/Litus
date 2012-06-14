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
    CudiBundle\Form\Prof\Prof\Add as AddForm,
    SyllabusBundle\Entity\SubjectProfMap;

/**
 * ProfController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CudiBundle\Component\Controller\ProfController
{
	public function addAction()
    {
        if (!($subject = $this->_getSubject()))
            return;
            
        if (!($academicYear = $this->_getAcademicYear()))
        	return
            
        $form = new AddForm();
        
        if($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->post()->toArray();
	    	
	    	if ($form->isValid($formData)) {
	    	    $docent = $this->getEntityManager()
	    	        ->getRepository('CommonBundle\Entity\Users\People\Academic')
	    	        ->findOneById($formData['prof_id']);
	    	        
	    	    $mapping = $this->getEntityManager()
	    	        ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
	    	        ->findOneBySubjectAndProfAndAcademicYear($subject, $docent, $academicYear);
	    	    
	    	    if (null === $mapping) {
    	    	    $mapping = new SubjectProfMap($subject, $docent, $academicYear);
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
        
        if ($mapping->getProf()->getId() == $this->getAuthentication()->getPersonObject()->getId()) {
            return array(
                'result' => (object) array("status" => "error")
            );
        }
        
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
    
    private function _getSubject($id = null)
    {
        if (!($academicYear = $this->_getAcademicYear()))
        	return
        	
        $id = $id == null ? $this->getParam('id') : $id;

        if (null === $id) {
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
    
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $id,
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );
            
    	
    	if (null === $mapping) {
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
    	
    	return $mapping->getSubject();
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
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneById($this->getParam('id'));

    	if (null === $mapping || null === $this->_getSubject($mapping->getSubject()->getId())) {
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
}