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

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\Users\Credential,
    CommonBundle\Entity\Users\Code,
    CommonBundle\Entity\Users\People\Academic,
    DateTime,
    Doctrine\ORM\EntityManager,
    SimpleXMLElement,
    SyllabusBundle\Entity\AcademicYearMap,
    SyllabusBundle\Entity\Study as StudyEntity,
    SyllabusBundle\Entity\Subject as SubjectEntity,
    SyllabusBundle\Entity\SubjectProfMap,
    SyllabusBundle\Entity\StudySubjectMap,
    Zend\Http\Client as HttpClient,
    Zend\Dom\Query as DomQuery;

/**
 * Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Study
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_entityManager;
    
    /**
     * @var array
     */
    private $_profs;
    
    /**
     * @var array
     */
    private $_callback;
    
    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $_academicYear;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param string $xmlPath
     * @param array $callback
     */
    public function __construct(EntityManager $entityManager, $xmlPath, $callback)
    {
        $this->_entityManager = $entityManager;
        $this->_prof = array();
        $this->_callback = $callback;
        
        $this->_callback('load_xml', 'SC_51016934.xml');
        
        $xml = simplexml_load_file('http://litus/admin/syllabus/update/xml');
        
        $startAcademicYear = AcademicYear::getStartOfAcademicYear(
            new DateTime(substr($xml->properties->academiejaar, 0, 4) . '-12-25 0:0')
        );
        $academicYear = $entityManager->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStartDate($startAcademicYear);

        if (null === $academicYear) {
            $endAcademicYear = AcademicYear::getStartOfAcademicYear(
                new DateTime((substr($xml->properties->academiejaar, 0, 4) + 1) . '-12-25 0:0')
            );
            $academicYear = new AcademicYearEntity($startAcademicYear, $endAcademicYear);
            $entityManager->persist($academicYear);
        }
        $this->_academicYear = $academicYear;
        
        $this->_callback('cleanup', '');

        $this->_removeMappings();
        
        $this->_createSubjects(
            $xml->data->sc->cg, 
            $this->_createStudies($xml->data->sc)
        );
        
        $this->_callback('saving_data', (string) $xml->data->sc->titel);
        
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
        $this->_callback('create_studies', (string) $data->titel);
        $studies = array();
        
        $language = trim((string) $data->doceertaal);
        $mainTitle = ucfirst(trim((string) $data->titel));
                
        foreach($data->fases->children() as $phase) {
		    $phaseNumber = (int) $phase->attributes()->code;
            
            $subStudies = array();
            $studies[$phaseNumber] = array();
            
            $mainStudy = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study')
                ->findOneByTitlePhaseAndLanguage($mainTitle, $phaseNumber, $language);
            if (null == $mainStudy) {
                $mainStudy = new StudyEntity($mainTitle, $phaseNumber, $language);
                $this->getEntityManager()->persist($mainStudy);
            }
            
		    if ($phase->tcs->children()->count() > 0) {
		        foreach($phase->tcs->children() as $studyData) {
		            $title = preg_replace('/\([a-zA-Z0-9\s]*\)/', '', $studyData->titel);
		            $titles = explode('+', $title);
		            
		            if (sizeof($titles) == 2) {
		                if (isset($subStudies[$titles[0]])) {
		                    $subStudy = $subStudies[$titles[0]];
		                } else {
		                    $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $titles[0])));
		                    
		                    $subStudy = $this->getEntityManager()
		                        ->getRepository('SyllabusBundle\Entity\Study')
		                        ->findOneByTitlePhaseLanguageAndParent($subTitle, $phaseNumber, $language, $mainStudy);
		                    if (null == $subStudy) {
		                        $subStudy = new StudyEntity($subTitle, $phaseNumber, $language, $mainStudy);
		                        $this->getEntityManager()->persist($subStudy);
		                    }
		                    $subStudies[$titles[0]] = $subStudy;
		                }
		                
		                $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $titles[1])));
		                $study = $this->getEntityManager()
		                    ->getRepository('SyllabusBundle\Entity\Study')
		                    ->findOneByTitlePhaseLanguageAndParent($subTitle, $phaseNumber, $language, $subStudy);
		                if (null == $study) {
		                    $study = new StudyEntity($subTitle, $phaseNumber, $language, $subStudy);
		                    $this->getEntityManager()->persist($study);
		                }
		            } else {
		                $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $title)));
		                $study = $this->getEntityManager()
		                    ->getRepository('SyllabusBundle\Entity\Study')
		                    ->findOneByTitlePhaseLanguageAndParent($subTitle, $phaseNumber, $language, $mainStudy);
		                if (null == $study) {
		                    $study = new StudyEntity($subTitle, $phaseNumber, $language, $mainStudy);
		                    $this->getEntityManager()->persist($study);
		                }
		            }
		            $studies[$phaseNumber][(int) $studyData->attributes()->objid] = $study;
		            $this->getEntityManager()->persist(new AcademicYearMap($study, $this->_academicYear));
		        }
		    } else {
		        $studies[$phaseNumber][0] = $mainStudy;
		    }
		    $this->getEntityManager()->flush();
		}
		return $studies;
    }
    
    private function _createSubjects($data, $studies)
    {
        $this->_callback('create_subjects');
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
                    $this->_callback('create_subjects', (string) $subjectData->titel);
                    
                    $subject = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject')
                        ->findOneByCode($code);
                    
                    if (null === $subject) {
                        $subject = new SubjectEntity($code, trim((string) $subjectData->titel), (int) $subjectData->periode, (int) $subjectData->pts);
                        $this->getEntityManager()->persist($subject);
                    }
                    $this->_createProf($subject, $subjectData->docenten->children());
                    
                    $mandatory = $subjectData->attributes()->verplicht == 'J' ? true : false;

                    foreach($subjectData->fases->children() as $phase) {
                        $phaseNumber = (int) $phase;
                        if (is_array($activeStudies[$phaseNumber])) {
                            foreach($activeStudies[$phaseNumber] as $activeStudy) {
                                $map = new StudySubjectMap($activeStudy, $subject, $mandatory, $this->_academicYear);
                                $this->getEntityManager()->persist($map);
                            }
                        } else {
                            $map = new StudySubjectMap($activeStudies[$phaseNumber], $subject, $mandatory, $this->_academicYear);
                            $this->getEntityManager()->persist($map);
                        }
                    }
                    $this->getEntityManager()->flush();
                }
            }
            
            if ($subjects->cg->count() > 0) {
                $this->_createSubjects($subjects, $activeStudies);
            }
        }
    }
    
    private function _createProf(SubjectEntity $subject, $profs)
    {
        foreach($profs as $profData) {
            $identification = 'u' . substr(trim($profData->attributes()->persnr), 1);
            
            $prof = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                ->findOneByUniversityIdentification($identification);
            if (null == $prof) {
                if (isset($this->_profs[$identification])) {
                    $prof = $this->_profs[$identification];
                } else {
                    $info = $this->_getInfoProf(trim($profData->attributes()->persnr));
                    
                    $prof = new Academic(
                        $identification,
                        array(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\Acl\Role')
                                ->findOneByName('prof')
                        ),
                        trim($profData->voornaam),
                        trim($profData->familienaam),
                        $info['email'],
                        $info['phone'],
                        null,
                        $identification);
                    
                    do {
                    	$code = md5(uniqid(rand(), true));
                    	$found = $this->_entityManager
                    	    ->getRepository('CommonBundle\Entity\Users\Code')
                    	    ->findOneByCode($code);
                    } while(isset($found));
                    
                    $code = new Code($code);
                    $this->getEntityManager()->persist($code);
                    $prof->setCode($code);
                    
                    $this->getEntityManager()->persist($prof);
                    $this->_profs[$identification] = $prof;
                }
            }
            
            $map = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                ->findOneBySubjectAndProfAndAcademicYear($subject, $prof, $this->_academicYear);
            if (null == $map) {
                $map = new SubjectProfMap($subject, $prof, $this->_academicYear);
                $this->getEntityManager()->persist($map);
            }
            $this->getEntityManager()->flush();
        }
    }
    
    private function _removeMappings()
    {
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\AcademicYearMap')
            ->findByAcademicYear($this->_academicYear);

        foreach($mapping as $map)
            $this->getEntityManager()->remove($map);
                    
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
            ->findByAcademicYear($this->_academicYear);

        foreach($mapping as $map)
            $this->getEntityManager()->remove($map);
            
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findByAcademicYear($this->_academicYear);

        foreach($mapping as $map)
            $this->getEntityManager()->remove($map);
        
        $this->getEntityManager()->flush();
    }
    
    private function _getInfoProf($identification)
    {
        $client = new HttpClient();
        $response = $client->setUri('http://www.kuleuven.be/wieiswie/nl/person/' . $identification)
            ->send();

        preg_match('/<noscript>([a-zA-Z0-9\[\]\s\-]*)<\/noscript>/', $response->getBody(), $matches);
        if (sizeof($matches) > 1)
            $email = str_replace(array(' [dot] ', ' [at] '), array('.', '@'), $matches[1]);
        else
            $email = null;

        preg_match('/tel\s\.([0-9\+\s]*)/', $response->getBody(), $matches);
        if (sizeof($matches) > 1)
            $phone = trim(str_replace(' ', '', $matches[1]));
        else
            $phone = null;
        
        return array(
            'email' => $email,
            'phone' => $phone,
        );
    }
    
    private function _callback($type, $extra = null)
    {
        call_user_func($this->_callback, $type, $extra);
    }
}