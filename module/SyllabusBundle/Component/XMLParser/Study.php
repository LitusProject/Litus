<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Component\XMLParser;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\Study as StudyEntity,
    SyllabusBundle\Entity\Study\AcademicYearMap,
    SyllabusBundle\Entity\Study\SubjectMap,
    SyllabusBundle\Entity\Subject as SubjectEntity,
    SyllabusBundle\Entity\Subject\ProfMap,
    Zend\Http\Client as HttpClient,
    Zend\Mail\Transport\TransportInterface,
    finfo;

/**
 * Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Study
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TransportInterface
     */
    private $mailTransport;

    /**
     * @var array
     */
    private $profs;

    /**
     * @var array
     */
    private $callback;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $academicYear;

    /**
     * @var array
     */
    private $subjects;

    /**
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     * @param array              $callback
     */
    public function __construct(EntityManager $entityManager, TransportInterface $mailTransport, $callback)
    {
        $this->entityManager = $entityManager;
        $this->mailTransport = $mailTransport;
        $this->callback = $callback;

        if ($this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')->getConfigValue('syllabus.enable_update') != '1') {
            return;
        }

        $this->academicYear = $this->getAcademicYear();
        $urls = $this->getUrls();

        $this->callback('progress', 1);

        $counter = 0;

        $this->cleanUpAcademicYear();
        $this->callback('cleanup', '');

        foreach ($urls as $url) {
            $counter++;
            $entityManager->clear();
            $this->callback('load_xml', substr($url, strrpos($url, '/') + 1));

            $this->academicYear = $this->getAcademicYear();

            $xml = simplexml_load_file($url);

            $this->subjects = array();
            $this->profs = array();

            $this->createSubjects(
                $xml->data->programma->modulegroep,
                $this->createStudies($xml->data->programma)
            );

            $this->callback('saving_data', (string) $xml->data->titel);

            $this->getEntityManager()->flush();

            $this->callback('progress', round($counter/count($urls)*100, 4));
        }
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param  SimpleXMLElement $data
     * @return array
     */
    private function createStudies($data)
    {
        $this->callback('create_studies', (string) $data->titel);
        $studies = array();

        $language = trim((string) $data->doceertaal->code);
        $mainTitle = html_entity_decode(ucfirst(trim((string) $data->titel)));

        foreach ($data->fases->children() as $phase) {
            $phaseNumber = (int) $phase->attributes()->code;

            $subStudies = array();
            $studies[$phaseNumber] = array();

            $mainStudy = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study')
                ->findOneByKulId($data->attributes()->id);
            if (null == $mainStudy) {
                $mainStudy = new StudyEntity();
                $mainStudy->setTitle($mainTitle)
                    ->setKulId($data->attributes()->objid)
                    ->setPhase($phaseNumber)
                    ->setLanguage($language);
                $this->getEntityManager()->persist($mainStudy);
            } else {
                $mainStudy->setTitle($mainTitle);
            }

            if ($phase->toegestane_combinaties->children()->count() > 0) {
                foreach ($phase->toegestane_combinaties->children() as $studyData) {
                    $title = html_entity_decode(preg_replace('/\([a-zA-Z0-9\s]*\)/', '', $studyData->titel));
                    $titles = explode('+', $title);

                    if (count($titles) == 2) {
                        if (isset($subStudies[(string) $studyData->afstudeerrichting->attributes()->id])) {
                            $subStudy = $subStudies[(string) $studyData->afstudeerrichting->attributes()->id];
                        } else {
                            $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $titles[0])));

                            $subStudy = $this->getEntityManager()
                                ->getRepository('SyllabusBundle\Entity\Study')
                                ->findOneByKulId($studyData->afstudeerrichting->attributes()->id);
                            if (null == $subStudy) {
                                $subStudy = new StudyEntity();
                                $subStudy->setTitle($subTitle)
                                    ->setKulId($studyData->afstudeerrichting->attributes()->id)
                                    ->setPhase($phaseNumber)
                                    ->setLanguage($language)
                                    ->setParent($mainStudy);
                                $this->getEntityManager()->persist($subStudy);
                            } else {
                                $subStudy->setTitle($subTitle);
                            }
                            $subStudies[(string) $studyData->afstudeerrichting->attributes()->id] = $subStudy;
                        }

                        $title = $titles[1];
                    } else {
                        $subStudy = $mainStudy;
                    }

                    $subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $title)));
                    $study = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Study')
                        ->findOneByKulId($studyData->attributes()->id);
                    if (null == $study) {
                        $study = new StudyEntity();
                        $subStudy->setTitle($subTitle)
                            ->setKulId($studyData->attributes()->id)
                            ->setPhase($phaseNumber)
                            ->setLanguage($language)
                            ->setParent($subStudy);
                        $this->getEntityManager()->persist($study);
                    } else {
                        $study->setTitle($subTitle);
                    }

                    $studies[$phaseNumber][(int) $studyData->attributes()->id] = $study;
                    $map = new AcademicYearMap($study, $this->academicYear);
                    $this->getEntityManager()->persist($map);
                }
            } else {
                $studies[$phaseNumber][0] = $mainStudy;
                $map = new AcademicYearMap($mainStudy, $this->academicYear);
                $this->getEntityManager()->persist($map);
            }
        }

        return $studies;
    }

    /**
     * @param array $data
     * @param array $studies
     */
    private function createSubjects($data, $studies)
    {
        $this->callback('create_subjects');
        if (!isset($data->modulegroep)) {
            return;
        }

        foreach ($data->modulegroep as $subjects) {
            if ($subjects->tonen_in_programmagids != 'True') {
                continue;
            }

            if ($subjects->toegestane_combinaties->children()->count() > 0) {
                $activeStudies = array();
                foreach ($studies as $phaseNumber => $phase) {
                    if (is_array($phase)) {
                        $activeStudies[$phaseNumber] = array();
                        foreach ($phase as $studyId => $study) {
                            foreach ($subjects->toegestane_combinaties->children() as $objId) {
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

            if ($subjects->opleidingsonderdelen->children()->count() > 0) {
                foreach ($subjects->opleidingsonderdelen->opleidingsonderdeel as $subjectData) {
                    $code = trim((string) $subjectData->attributes()->code);
                    $this->callback('create_subjects', (string) $subjectData->titel);

                    $subject = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject')
                        ->findOneByCode($code);

                    if (null === $subject) {
                        if (isset($this->subjects[$code])) {
                            $subject = $this->subjects[$code];
                        } else {
                            $subject = new SubjectEntity();
                            $subject->setCode($code)
                                ->setName(html_entity_decode(trim((string) $subjectData->titel)))
                                ->setSemester((int) $subjectData->aanbodperiode)
                                ->setCredits((int) $subjectData->pts);
                            $this->getEntityManager()->persist($subject);
                        }
                    } else {
                        $subject->setName(html_entity_decode(trim((string) $subjectData->titel)));
                    }

                    if (!isset($this->subjects[$code])) {
                        $this->createProf($subject, $subjectData->docenten->children());
                    }

                    $this->subjects[$code] = $subject;

                    $mandatory = $subjectData->attributes()->verplicht == 'True' ? true : false;

                    foreach ($subjectData->fases->children() as $phase) {
                        $phaseNumber = (int) $phase;

                        if (is_array($activeStudies[$phaseNumber])) {
                            foreach ($activeStudies[$phaseNumber] as $activeStudy) {
                                $map = new SubjectMap($activeStudy, $subject, $mandatory, $this->academicYear);
                                $this->getEntityManager()->persist($map);
                            }
                        } else {
                            $map = new SubjectMap($activeStudies[$phaseNumber], $subject, $mandatory, $this->academicYear);
                            $this->getEntityManager()->persist($map);
                        }
                    }
                }
            }

            if ($subjects->modulegroep->count() > 0) {
                $this->createSubjects($subjects, $activeStudies);
            }
        }
    }

    /**
     * @param SubjectEntity $subject
     * @param array         $profs
     */
    private function createProf(SubjectEntity $subject, $profs)
    {
        $maps = array();
        foreach ($profs as $profData) {
            $identification = 'u' . substr(trim($profData->attributes()->persno), 1);

            $prof = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($identification);
            if (null == $prof) {
                if (isset($this->profs[$identification])) {
                    $prof = $this->profs[$identification];
                } else {
                    $info = $this->getInfoProf(trim($profData->attributes()->persno));

                    $prof = new Academic();
                    $prof->setUsername($identification)
                        ->setRoles(
                            array(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\Acl\Role')
                                    ->findOneByName('prof'),
                            )
                        )
                        ->setFirstName(trim($profData->voornaam))
                        ->setLastName(trim($profData->familienaam))
                        ->setEmail($info['email'])
                        ->setPersonalEmail($info['email'])
                        ->setUniversityEmail($info['email'])
                        ->setPhoneNumber($info['phone'])
                        ->setUniversityIdentification($identification)
                        ->activate($this->getEntityManager(), $this->mailTransport, true);

                    $image = $this->getProfImage($identification, $info['photo']);
                    if (null !== $image) {
                        $prof->setPhotoPath($image);
                    }

                    $this->getEntityManager()->persist($prof);
                    $this->profs[$identification] = $prof;
                }
            }

            if ($prof->canHaveUniversityStatus($this->academicYear)) {
                $prof->addUniversityStatus(
                    new UniversityStatus(
                        $prof,
                        'professor',
                        $this->academicYear
                    )
                );
            }

            $map = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findOneBySubjectAndProfAndAcademicYear($subject, $prof, $this->academicYear);
            if (null === $map) {
                if (!isset($maps[$prof->getUniversityIdentification()])) {
                    $map = new ProfMap($subject, $prof, $this->academicYear);
                    $this->getEntityManager()->persist($map);
                    $maps[$prof->getUniversityIdentification()] = $map;
                }
            }
        }
    }

    /**
     * @return void
     */
    private function cleanUpAcademicYear()
    {
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\AcademicYearMap')
            ->findByAcademicYear($this->academicYear);

        foreach ($mapping as $map) {
            $this->getEntityManager()->remove($map);
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
            ->findByAcademicYear($this->academicYear);

        foreach ($mapping as $map) {
            $this->getEntityManager()->remove($map);
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findByAcademicYear($this->academicYear);

        foreach ($mapping as $map) {
            $this->getEntityManager()->remove($map);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param  string $identification
     * @return array
     */
    private function getInfoProf($identification)
    {
        $returnArray = array(
            'email' => null,
            'phone' => null,
            'photo' => 'http://www.kuleuven.be/wieiswie/nl/person/' . $identification . '/photo',
        );

        $client = new HttpClient();
        $response = $client->setUri('http://www.kuleuven.be/wieiswie/nl/person/' . $identification)
            ->send();

        preg_match('/<noscript>([a-zA-Z0-9\[\]\s\-]*)<\/noscript>/', $response->getBody(), $matches);
        if (count($matches) > 1) {
            $returnArray['email'] = str_replace(array(' [dot] ', ' [at] '), array('.', '@'), $matches[1]);
        } else {
            $returnArray['email'] = null;
        }

        preg_match('/tel\s\.([0-9\+\s]*)/', $response->getBody(), $matches);
        if (count($matches) > 1) {
            $returnArray['phone'] = trim(str_replace(' ', '', $matches[1]));
        } else {
            $returnArray['phone'] = null;
        }

        return $returnArray;
    }

    /**
     * @param  string      $identification
     * @param  string      $url
     * @return string|null
     */
    private function getProfImage($identification, $url)
    {
        $headers = get_headers($url);
        if ($headers[0] != 'HTTP/1.1 404 Not Found' && $headers[0] != 'HTTP/1.1 302 Moved Temporarily') {
            file_put_contents('/tmp/' . $identification, file_get_contents($url));
            $finfo = new finfo();
            $fileinfo = $finfo->file('/tmp/' . $identification, FILEINFO_MIME);
            $mimetype = substr($fileinfo, 0, strpos($fileinfo, ';'));

            if (in_array($mimetype, array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif'))) {
                $filePath = 'public' . $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path');

                do {
                    $fileName = '/' . sha1(uniqid());
                } while (file_exists($filePath . $fileName));

                file_put_contents($filePath . $fileName, file_get_contents('/tmp/' . $identification));
                unlink('/tmp/' . $identification);

                return $fileName;
            }
        }
    }

    /**
     * @param string      $type
     * @param string|null $extra
     */
    private function callback($type, $extra = null)
    {
        call_user_func($this->callback, $type, $extra);
    }

    /**
     * @return array
     */
    private function getUrls()
    {
        $url = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.root_xml');

        $url = str_replace('{{ year }}', $this->academicYear->getStartDate()->format('Y'), $url);

        $departments = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.department_ids')
            );

        $this->callback('load_xml', substr($url, strrpos($url, '/') + 1));

        $root = simplexml_load_file($url);

        $studies = array();

        foreach ($root->data->children() as $organization) {
            foreach ($organization->children() as $department) {
                if (in_array($department->attributes()->id, $departments)) {
                    foreach ($department->kwalificatie->children() as $classification) {
                        foreach ($classification->graad as $grade) {
                            foreach ($grade->opleidingen->children() as $study) {
                                $studies[] = array(
                                    'id' => (string) $study->attributes()->id,
                                    'language' => $study->taal,
                                );
                            }
                        }
                    }
                }
            }
        }

        $departmentUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.department_url');
        $departmentUrl = str_replace('{{ year }}', $this->academicYear->getStartDate()->format('Y'), $departmentUrl);

        $studyUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.study_url');
        $studyUrl = str_replace('{{ year }}', $this->academicYear->getStartDate()->format('Y'), $studyUrl);

        $urls = array();

        foreach ($studies as $study) {
            $url = str_replace(
                array(
                    '{{ language }}',
                    '{{ id }}',
                ),
                array(
                    $study['language'],
                    $study['id'],
                ),
                $departmentUrl
            );
            $this->callback('load_xml', substr($url, strrpos($url, '/') + 1));
            $xml = simplexml_load_file($url);

            foreach ($xml->data->opleiding->programmas->children() as $study) {
                $urls[] = str_replace(
                    array(
                        '{{ language }}',
                        '{{ id }}',
                    ),
                    array(
                        strtolower((string) $study->taal->code),
                        (string) $study->attributes()->id,
                    ),
                    $studyUrl
                );
            }
        }

        return $urls;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function getAcademicYear()
    {
        return AcademicYear::getOrganizationYear($this->getEntityManager());
    }
}
