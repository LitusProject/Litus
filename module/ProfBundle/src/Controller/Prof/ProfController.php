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
    ProfBundle\Form\Prof\Prof\Add as AddForm,
    SyllabusBundle\Entity\SubjectProfMap;

/**
 * ProfController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \ProfBundle\Component\Controller\ProfController
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
    			'prof_subject',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $subject = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneByIdAndProf($this->getParam('id'), $this->getAuthentication()->getPersonObject());
    	
    	if (null === $subject) {
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
}