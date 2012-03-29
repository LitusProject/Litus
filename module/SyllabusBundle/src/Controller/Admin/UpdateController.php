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
    public function xmlAction()
    {
    }
    
	public function updateAction()
	{
		$xml = simplexml_load_file('http://litus/admin/syllabus/xml');

		$studies = $this->_createStudies($xml->data->sc);
		$this->_createSubjects($xml->data->sc->cg, $studies);
		$this->getEntityManager()->flush();
		
		echo '<h1>Result</h1>';
		foreach($studies as $phase) {
		    if (is_array($phase)) {
		        foreach($phase as $study) {
		            echo '<h3>' . $study->getFullTitle() . ' - ' . $study->getPhase() . '</h3>';
		            $this->printSubjects($study);
		        }
		    } else {
		        echo '<h3>' . $phase->getFullTitle() . ' - ' . $phase->getPhase() . '</h3>';
		        $this->printSubjects($phase);
		    }
		}
		exit;
    }
    
    private function printSubjects($study)
    {
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
            ->findAllByStudy($study);
            
        foreach($mapping as $map) {
            echo ($map->isMandatory() ? '<b>' : '<i>') . $map->getSubject()->getName() . ($map->isMandatory() ? '</b>' : '</i>') . '<br>';
        }
    }
    
    private function _createStudies($data)
    {
        $studies = array();
        
        $language = trim((string) $data->doceertaal);
        $mainTitle = ucfirst(trim((string) $data->titel));

        foreach($data->fases->children() as $phase) {
		    $phaseNumber = (int) $phase->attributes()->code;
            
            $studies[$phaseNumber] = array();
            
            $mainStudy = new Study($mainTitle, $phaseNumber, $language);
            $this->getEntityManager()->persist($mainStudy);
            $studies[$phaseNumber][0] = $mainStudy;
            
		    if ($phase->tcs->children()->count() > 0) {
		        foreach($phase->tcs->children() as $studyData) {
                    $parent = $mainStudy;
		            $title = preg_replace('/\([a-zA-Z0-9\s]*\)/', '', 
		                str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $studyData->titel)
		            );
		            $titles = explode('+', $title);
		            foreach($titles as $subTitle) {
		                $parent = new Study(ucfirst(trim($subTitle)), $phaseNumber, $language, $parent);
		                $this->getEntityManager()->persist($parent);
		            }
		            $studies[$phaseNumber][(int) $studyData->attributes()->objid] = $parent;
		        }
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