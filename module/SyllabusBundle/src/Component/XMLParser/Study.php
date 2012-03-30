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
 
namespace SyllabusBundle\Component\XMLParser;

use Doctrine\ORM\EntityManager,
    SimpleXMLElement,
    SyllabusBundle\Entity\Study as StudyEntity,
    SyllabusBundle\Entity\StudySubjectMap,
    SyllabusBundle\Entity\Subject as SubjectEntity;

/**
 * Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Study
{
    private $_entityManager;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, SimpleXMLElement $xml)
    {
        $this->_entityManager = $entityManager;
        
        $this->_createSubjects(
            $xml->data->sc->cg, 
            $this->_createStudies($xml->data->sc)
        );
        
        $this->getEntityManager()->flush();
    }
    
    /**
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->_entityManager;
    }
    
    private function _createStudies($data)
    {
        $studies = array();
        
        $language = trim((string) $data->doceertaal);
        $mainTitle = ucfirst(trim((string) $data->titel));
                
        foreach($data->fases->children() as $phase) {
		    $phaseNumber = (int) $phase->attributes()->code;
            
            $subStudies = array();
            $studies[$phaseNumber] = array();
            
            $mainStudy = new StudyEntity($mainTitle, $phaseNumber, $language);
            $this->getEntityManager()->persist($mainStudy);
            
		    if ($phase->tcs->children()->count() > 0) {
		        foreach($phase->tcs->children() as $studyData) {
		            $title = preg_replace('/\([a-zA-Z0-9\s]*\)/', '', $studyData->titel);
		            $titles = explode('+', $title);
		            
		            if (sizeof($titles) == 2) {
		                if (isset($subStudies[$titles[0]])) {
		                    $subStudy = $subStudies[$titles[0]];
		                } else {
		                    $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $titles[0])));
		                    $subStudy= new StudyEntity($subTitle, $phaseNumber, $language, $mainStudy);
		                    $this->getEntityManager()->persist($subStudy);
		                    $subStudies[$titles[0]] = $subStudy;
		                }
		                
		                $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $titles[1])));
		                $study= new StudyEntity($subTitle, $phaseNumber, $language, $subStudy);
		                $this->getEntityManager()->persist($study);
		            } else {
		                $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $title)));
		                $study = new StudyEntity($subTitle, $phaseNumber, $language, $mainStudy);
		                $this->getEntityManager()->persist($study);
		            }
		            $studies[$phaseNumber][(int) $studyData->attributes()->objid] = $study;
		        }
		    } else {
		        $studies[$phaseNumber][0] = $mainStudy;
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
                        $subject = new SubjectEntity($code, trim((string) $subjectData->titel), (int) $subjectData->periode, (int) $subjectData->pts);
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