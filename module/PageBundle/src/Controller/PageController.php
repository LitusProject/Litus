<?php

namespace PageBundle\Controller;

/**
 * Handles system page controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PageController extends \CommonBundle\Component\Controller\ActionController\CommonController
{
    public function viewAction()
    {
        if (is_numeric($this->getParam('id'))) {
            if (!($page = $this->_getTranslationById()))
                return;
        } else {
            if (!($page = $this->_getTranslationByName()))
                return;
        }
        
        
        return array(
            'page' => $page,
        );
    }
    
    public function _getTranslationByName()
    {
    	if (null === $this->getParam('id')) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}
    
        $translation = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Nodes\Translation')
            ->findOneByName($this->getParam('id'));
    	
    	if (null === $translation || $translation->getLanguage() != $this->getLanguage()) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}
    	
    	return $translation;
    }
    
    public function _getTranslationById()
    {
    	if (null === $this->getParam('id')) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}
    
        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $page) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}
    	
    	return $page->getTranslation($this->getLanguage());
    }
}