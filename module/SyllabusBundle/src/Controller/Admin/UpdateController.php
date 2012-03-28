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
    SyllabusBundle\Entity\StudySubjectMap,
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

		$studies = $this->_createStudies($xml->data->sc);
		$this->_createSubjects($xml->data->sc->cg, $studies);
		$this->getEntityManager()->flush();
    }
    
    private function _createStudies($data)
    {
        $studies = array();
        
        $language = trim((string) $data->doceertaal);
        $title = trim((string) $data->titel);

        foreach($data->fases->children() as $phase) {
		    $phaseNumber = (int) $phase->attributes()->code;

		    if ($phase->tcs->children()->count() > 0) {
		        $studies[$phaseNumber] = array();
		        foreach($phase->tcs->children() as $minor) {
		            $study = new Study($title, trim((string) $minor->titel), $phaseNumber, $language);
		            $this->getEntityManager()->persist($study);
		            $studies[$phaseNumber][(int) $minor->attributes()->objid] = $study;
		        }
		        // what with majors and minors
		    } else {
		        $study = new Study($title, '', $phaseNumber, $language, '');
		        $this->getEntityManager()->persist($study);
		        $studies[$phaseNumber] = $study;
		    }
		}
		return $studies;
    }
    
    private function _createSubjects($data, $studies)
    {
        foreach($data->cg as $subjects) {
            if ($subjects->tc_cgs->children()->count() > 0) {
                $activeStudies = array();
                foreach($studies as $phaseNumber => $phase) {
                    if (is_array($phase)) {
                        $activeStudies[$phaseNumber] = array();
                        foreach($phase as $studyId => $study) {
                            foreach($subjects->tc_cgs->children() as $objId) {
                                if ($studyId == (string) $objId) {
                                    $activeStudies[$phaseNumber][$studyId] = $study;
                                    break;
                                }
                            }
                        }
                    } else {
                        $activeStudies[$phaseNumber] = $phase;
                    }
                }
            } else {
                $activeStudies = $studies;
            }
            
            if ($subjects->opos->children()->count() > 0) {
                foreach($subjects->opos->opo as $subjectData) {
                    $code = trim((string) $subjectData->attributes()->short);
                    $subject = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject')
                        ->findOneByCode($code);
                    
                    if (null === $subject) {
                        $subject = new Subject($code, trim((string) $subjectData->titel), (int) $subjectData->periode, (int) $subjectData->pts);
                        $this->getEntityManager()->persist($subject);
                    }
                    
                    $mandatory = $subjectData->attributes()->verplicht == 'J' ? true : false;

                    foreach($subjectData->fases->children() as $phase) {
                        $phaseNumber = (int) $phase;
                        if (is_array($activeStudies[$phaseNumber])) {
                            foreach($activeStudies[$phaseNumber] as $activeStudy) {
                                $map = new StudySubjectMap($activeStudy, $subject, $mandatory);
                                $this->getEntityManager()->persist($map);
                            }
                        } else {
                            $map = new StudySubjectMap($activeStudies[$phaseNumber], $subject, $mandatory);
                            $this->getEntityManager()->persist($map);
                        }
                    }
                }
            }
            
            if ($subjects->cg->count() > 0) {
                $this->_createSubjects($subjects, $activeStudies);
            }
        }
    }
}