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

use CudiBundle\Entity\Article;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \ProfBundle\Component\Controller\ProfController
{
	public function indexAction()
	{
	    if ($this->getAuthentication()->isAuthenticated()) {
      	    $this->paginator()->setItemsPerPage(5);
    	    $paginator = $this->paginator()->createFromArray(
    	    	$this->getEntityManager()
    	    	    ->getRepository('ProfBundle\Entity\Action')
    	    	    ->findAllActionsBySubjectPerson($this->getAuthentication()->getPersonObject()),
    	        $this->getParam('page')
    	    );
    	    
    	    foreach($paginator as $action) {
    	        if ($action->getEntity() == 'article')
    	            $this->applyEditsArticle($action->getArticle());
    	        if ($action->getEntity() == 'file')
    	            $this->applyEditsArticle($action->getFile()->getInternalArticle());
    	        if ($action->getEntity() == 'mapping')
    	            $this->applyEditsArticle($action->getArticleSubjectMap()->getArticle());
    	    }
    	    	    
    	    return array(
    	        'paginator' => $paginator,
            	'paginationControl' => $this->paginator()->createControl(),
    	    );
        }
        
        return array();
	}
}