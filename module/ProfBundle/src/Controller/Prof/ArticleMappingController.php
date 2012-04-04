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
    ProfBundle\Entity\Action\Mapping\Remove as RemoveAction;

/**
 * ArticleMappingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleMappingController extends \ProfBundle\Component\Controller\ProfController
{
    public function addAction()
    {
    	return array();
    }
    
    public function deleteAction()
    {
    	$this->initAjax();
    	
		$mapping = $this->_getMapping();
        
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
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
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