<?php

namespace NewsBundle\Controller;

/**
 * Handles system news controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NewsController extends \CommonBundle\Component\Controller\ActionController\CommonController
{
    public function overviewAction()
    {
        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Nodes\News')
            ->findAll();
        
        return array(
            'news' => $news,
        );
    }

    public function viewAction()
    {
        if (!($news = $this->_getTranslationByName()))
            return;
            
        return array(
            'news' => $news,
        );
    }
    
    public function _getTranslationByName()
    {
    	if (null === $this->getParam('name')) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}
    
        $translation = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Nodes\Translation')
            ->findOneByName($this->getParam('name'));
    	
    	if (null === $translation || $translation->getLanguage() != $this->getLanguage()) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}
    	
    	return $translation;
    }
}