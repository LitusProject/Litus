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

use CommonBundle\Component\FlashMessenger\FlashMessage;

/**
 * StudyController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StudyController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('SyllabusBundle\Entity\Study')
        	    ->findAllStudies(),
            $this->getParam('page')
        );
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function searchAction()
    {
        $this->initAjax();
        
        switch($this->getParam('field')) {
        	case 'name':
        		$studies = $this->getEntityManager()
        			->getRepository('SyllabusBundle\Entity\Study')
        			->findAllByTitle($this->getParam('string'));
        		break;
        }
        
        $numResults = $this->getEntityManager()
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('search_max_results');
        
        array_splice($studies, $numResults);
        
        $result = array();
        foreach($studies as $study) {
        	$item = (object) array();
        	$item->id = $study->getId();
        	$item->title = $study->getFullTitle();
        	$item->phase = $study->getPhase();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
    }
}