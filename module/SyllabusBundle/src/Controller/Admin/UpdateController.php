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

use SyllabusBundle\Entity\Study,
    SyllabusBundle\Entity\Subject;

/**
 * UpdateController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class UpdateController extends \CommonBundle\Component\Controller\ActionController
{
	public function updateAction()
	{
		$xml = simplexml_load_file('http://localhost/SC_51016766.xml');
		
		$this->_createStudies($xml->data->sc);
		$this->_createSubjects($xml->data->sc->cg);
    }
    
    private function _createStudies($data)
    {
        $language = trim((string) $data->doceertaal);
        $title = trim((string) $data->titel);
        
        foreach($data->fases->children() as $phase) {
		    $phaseNumber = (int) $phase->attributes()->code;

		    if ($phase->tcs->children()->count() > 0) {
		        foreach($phase->tcs->children() as $minor) {
		            $study = new Study($title, (string) $minor->titel, $phaseNumber, $language, '');
		            $this->getEntityManager()->persist($study);
		        }
		        // what with majors and minors
		    } else {
		        $study = new Study($title, '', $phaseNumber, $language, '');
		        $this->getEntityManager()->persist($study);
		    }
		}
    }
    
    private function _createSubjects($data)
    {
        foreach($data->cg as $subjects) {
            if ($subjects->opos->children()->count() > 0) {
                foreach($subjects->opos->opo as $subject) {
                    $code = (string) $subject->attributes()->short;
                    $subject = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject')
                        ->findOneByCode($code);
                    
                    if (null === $subject) {
                        $subject = new Subject($code, (string) $subject->titel, (int) $subject->periode, (int) $subject->pts);
                        $this->getEntityManager()->persist($subject);
                    }
                    
                    // create mapping
                }
            }
            
            if ($subjects->cg->count() > 0) {
                $this->_createSubjects($subjects);
            }
        }
    }
}