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
    RuntimeException,
    SimpleXMLElement,
    SyllabusBundle\Entity\Study as StudyEntity,
    SyllabusBundle\Entity\Study\Combination,
    SyllabusBundle\Entity\Study\ModuleGroup,
    SyllabusBundle\Entity\Study\SubjectMap,
    SyllabusBundle\Entity\Subject,
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
    private $callback;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $academicYear;

    /**
     * @var array
     */
    private $subjectCache;

    /**
     * @var array
     */
    private $profCache;

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
        $this->academicYear = $this->getAcademicYear();
    }

    /**
     * @return void
     */
    public function update()
    {
        if ($this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')->getConfigValue('syllabus.enable_update') != '1') {
            return;
        }

        $urls = $this->getUrls();

        $this->callback('progress', 1);

        $counter = 0;

        $this->cleanUpAcademicYear();
        $this->callback('cleanup', '');

        foreach ($urls as $url) {
            $counter++;
            $this->getEntityManager()->clear();
            $this->academicYear = $this->getAcademicYear();

            $this->callback('load_xml', substr($url, strrpos($url, '/') + 1));

            $xml = simplexml_load_file($url);

            $this->subjectCache = array();
            $this->profCache = array();

            $this->callback('create_combinations', (string) $xml->data->programma->titel);
            $combinations = $this->createCombinations($xml->data->programma);
            $this->callback('create_module_groups', (string) $xml->data->programma->titel);
            $groups = $this->createModuleGroups($xml->data->programma, (string) $xml->data->programma->doceertaal->code);
            $this->connectModuleGroups($combinations, $groups);

            $this->callback('saving_data', (string) $xml->data->programma->titel);

            $this->getEntityManager()->flush();

            $this->callback('progress', round($counter/count($urls)*100, 4));
        }
    }

    /**
     * @param  SimpleXMLElement $xml
     * @return array
     */
    private function createCombinations(SimpleXMLElement $xml)
    {
        $combinations = array();

        $mainTitle = (string) $xml->titel;

        foreach ($xml->fases->children() as $phase) {
            $phaseNumber = (int) $phase->attributes()->code;
            if (sizeof($phase->toegestane_combinaties->children()) == 0) {
                $externalId = $phaseNumber . ((int) $xml->attributes()->id);
                $combination = $this->createCombination($phaseNumber, $externalId, $mainTitle);

                $combinations['_' . $externalId] = array(
                    'entity' => $combination,
                    'groups' => array(),
                );
            } else {
                foreach ($phase->toegestane_combinaties->children() as $data) {
                    $externalId = $phaseNumber . ((int) $data->attributes()->id);
                    $title = (string) $data->titel;
                    $title = str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $title);
                    $title = preg_replace('/\(\s*Afstudeerrichting[^)]+\)/i', '', $title);
                    $titleParts = explode('+', $title);
                    $title = '';
                    foreach ($titleParts as $part) {
                        $title .= ucfirst(trim($part)) . ' + ';
                    }
                    $title = trim($title, " \t\n\r\0\x0B+");
                    $combination = $this->createCombination($phaseNumber, $externalId, $mainTitle . ' - ' . $title);

                    $combinations['_' . $externalId] = array(
                        'entity' => $combination,
                        'groups' => array(),
                    );

                    foreach ($data->modulegroepen->children() as $group) {
                        $combinations['_' . $externalId]['groups'][] = (int) $group->attributes()->id;
                    }
                }
            }
        }

        return $combinations;
    }

    /**
     * @param  int         $phase
     * @param  int         $id
     * @param  string      $title
     * @return Combination
     */
    private function createCombination($phase, $id, $title)
    {
        $combination = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\Combination')
            ->findOneByExternalId($id);

        if (null === $combination) {
            $combination = new Combination();
            $this->getEntityManager()->persist($combination);
        }

        $combination->setPhase($phase)
            ->setExternalId($id)
            ->setTitle(html_entity_decode(trim($title)));

        $study = new StudyEntity();
        $study->setCombination($combination)
            ->setAcademicYear($this->academicYear);

        $this->getEntityManager()->persist($study);

        return $combination;
    }

    /**
     * @param  SimpleXMLElement $xml
     * @param  string           $language
     * @param  array            $parents
     * @return array
     */
    private function createModuleGroups(SimpleXMLElement $xml, $language, $parents = array())
    {
        $groups = array();

        foreach ($xml->modulegroep as $group) {
            if ($group->tonen_in_programmagids != 'True') {
                continue;
            }

            $phases = explode(',', $group->attributes()->fases);
            $currentParents = array();

            foreach ($phases as $phase) {
                $externalId = $phase . (int) $group->attributes()->id;
                $moduleGroup = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                    ->findOneByExternalId($externalId);

                if (null === $moduleGroup) {
                    $moduleGroup = new ModuleGroup();
                    $this->getEntityManager()->persist($moduleGroup);
                }

                $moduleGroup->setExternalId($externalId)
                    ->setTitle(html_entity_decode(trim($group->titel)))
                    ->setPhase($phase)
                    ->setLanguage(trim($language))
                    ->setMandatory($group->attributes()->verplicht == 'True');

                if ((int) $group->attributes()->niveau > 1) {
                    if (isset($parents[$phase])) {
                        $moduleGroup->setParent($parents[$phase]);
                    } elseif (sizeof($group->opleidingsonderdelen->children()) > 0) {
                        throw new RuntimeException('Module group ' . $externalId . ' has no parents.');
                    }
                }

                $currentParents[$phase] = $moduleGroup;
                $groups['_' . $externalId] = $moduleGroup;
            }

            if (sizeof($group->opleidingsonderdelen->children()) > 0) {
                $this->createSubjects($group->opleidingsonderdelen, $currentParents);
            }

            $groups = array_merge($groups, $this->createModuleGroups($group, $language, $currentParents));
        }

        return $groups;
    }

    /**
     * @param  SimpleXMLElement $xml
     * @param  array            $parents
     * @return void
     */
    private function createSubjects(SimpleXMLElement $xml, $parents = array())
    {
        foreach ($xml->children() as $data) {
            $this->callback('create_subject', html_entity_decode(trim((string) $data->titel)));

            $code = trim((string) $data->attributes()->code);

            $subject = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject')
                ->findOneByCode($code);

            if (null === $subject) {
                if (isset($this->subjectCache[$code])) {
                    $subject = $this->subjectCache[$code];
                } else {
                    $subject = new Subject();
                    $this->getEntityManager()->persist($subject);
                }
            }

            $subject->setCode($code)
                ->setName(html_entity_decode(trim((string) $data->titel)))
                ->setSemester((int) $data->aanbodperiode)
                ->setCredits((int) $data->studiepunten);

            $this->subjectCache[$code] = $subject;

            $this->createProfs($data->docenten, $subject);

            $mandatory = $data->attributes()->verplicht == 'True';

            foreach ($data->fases->children() as $phase) {
                $phase = (int) $phase;
                if (!isset($parents[$phase])) {
                    throw new RuntimeException('Subject ' . $code . ' has no parents.');
                }
                $map = new SubjectMap($parents[$phase], $subject, $mandatory, $this->academicYear);
                $this->getEntityManager()->persist($map);
            }
        }
    }

    /**
     * @param  SimpleXMLElement $xml
     * @param  Subject          $subject
     * @return void
     */
    private function createProfs(SimpleXMLElement $xml, Subject $subject)
    {
        $maps = array();

        foreach ($xml->children() as $data) {
            $identification = 'u' . substr(trim($data->attributes()->persno), 1);
            $prof = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($identification);

            if (null === $prof) {
                if (isset($this->profCache[$identification])) {
                    $prof = $this->profCache[$identification];
                } else {
                    $prof = new Academic();
                    $this->getEntityManager()->persist($prof);

                    $info = $this->getProfInfo(trim($data->attributes()->persno));

                    $prof->setUsername($identification)
                        ->setRoles(
                            array(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\Acl\Role')
                                    ->findOneByName('prof'),
                            )
                        )
                        ->setFirstName(trim($data->voornaam))
                        ->setLastName(trim($data->familienaam))
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
                    $this->profCache[$identification] = $prof;
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
     * @param  string $identification
     * @return array
     */
    private function getProfInfo($identification)
    {
        $info = array(
            'email' => null,
            'phone' => null,
            'photo' => 'http://www.kuleuven.be/wieiswie/nl/person/' . $identification . '/photo',
        );
        $client = new HttpClient();
        $response = $client->setUri('http://www.kuleuven.be/wieiswie/nl/person/' . $identification)
            ->send();

        preg_match('/<noscript>([a-zA-Z0-9\[\]\s\-]*)<\/noscript>/', $response->getBody(), $matches);

        if (count($matches) > 1) {
            $info['email'] = str_replace(array(' [dot] ', ' [at] '), array('.', '@'), $matches[1]);
        } else {
            $info['email'] = null;
        }

        preg_match('/tel\s\.([0-9\+\s]*)/', $response->getBody(), $matches);

        if (count($matches) > 1) {
            $info['phone'] = trim(str_replace(' ', '', $matches[1]));
        } else {
            $info['phone'] = null;
        }

        return $info;
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
     * @param  array $combinations
     * @param  array $moduleGroups
     * @return void
     */
    private function connectModuleGroups($combinations, $moduleGroups)
    {
        foreach ($combinations as $combination) {
            $groups = array();

            if (sizeof($combination['groups']) == 0) {
                foreach ($moduleGroups as $group) {
                    if ($group->getPhase() == $combination['entity']->getPhase() && null === $group->getParent()) {
                        $groups[] = $group;
                        break;
                    }
                }
            } else {
                foreach ($combination['groups'] as $id) {
                    if (isset($moduleGroups['_' . $combination['entity']->getPhase() . $id])) {
                        $groups[] = $moduleGroups['_' . $combination['entity']->getPhase() . $id];
                    }
                }
            }

            $combination['entity']->setModuleGroups($groups);
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
     * @return void
     */
    private function cleanUpAcademicYear()
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findByAcademicYear($this->academicYear);

        foreach ($studies as $study) {
            $this->getEntityManager()->remove($study);
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
