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
    CudiBundle\Entity\Article;

/**
 * SubjectController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllByProfAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->_getAcademicYear());
        
    	return array(
    	    'subjects' => $subjects,
    	);
    }
    
    public function subjectAction()
    {
        if (!($subject = $this->_getSubject()))
            return;
            
        if (!($academicYear = $this->_getAcademicYear()))
        	return;
        
        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
            ->findAllBySubjectAndAcademicYear($subject, $academicYear, true);
        
        $articleMappings = array();
        foreach($mappings as $mapping) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('mapping', $mapping->getId(), 'remove');
            
            if (!isset($actions[0]))
                $articleMappings[] = $mapping;
        }
          
        $profMappings = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllBySubjectAndAcademicYear($subject, $academicYear);
        
        return array(
            'subject' => $subject,
            'articleMappings' => $articleMappings,
            'profMappings' => $profMappings,
        );
    }
    
    public function typeaheadAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
        	return;
        
        $subjects = $this->getEntityManager()
        	->getRepository('SyllabusBundle\Entity\SubjectProfMap')
        	->findAllByNameAndProfAndAcademicYearTypeAhead($this->getParam('string'), $this->getAuthentication()->getPersonObject(), $academicYear);

        $result = array();
        foreach($subjects as $subject) {
        	$item = (object) array();
        	$item->id = $subject->getSubject()->getId();
        	$item->value = $subject->getSubject()->getCode() . ' - ' . $subject->getSubject()->getName();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
        }
    
    private function _getSubject()
    {
        if (!($academicYear = $this->_getAcademicYear()))
        	return
        	
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
    
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $this->getParam('id'),
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
}